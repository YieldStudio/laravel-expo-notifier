<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoNotificationsServiceInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Dto\PushReceiptResponse;
use YieldStudio\LaravelExpoNotifier\Dto\PushTicketResponse;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Events\InvalidExpoToken;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;

final class ExpoNotificationsService implements ExpoNotificationsServiceInterface
{
    public const SEND_NOTIFICATION_ENDPOINT = '/send';

    private PendingRequest $http;

    private ?Collection $expoMessages;

    private ?Collection $notificationsToSend;

    private ?Collection $notificationChunks;

    private Collection $tickets;

    private int $pushNotificationsPerRequestLimit;

    public function __construct(
        string $apiUrl,
        string $host,
        protected readonly ExpoPendingNotificationStorageInterface $notificationStorage,
        protected readonly ExpoTicketStorageInterface $ticketStorage
    ) {
        $this->pushNotificationsPerRequestLimit = config('expo-notifications.service.limits.push_notifications_per_request');

        $this->http = Http::withHeaders([
            'host' => $host,
            'accept' => 'application/json',
            'accept-encoding' => 'gzip, deflate',
            'content-type' => 'application/json',
        ])->baseUrl($apiUrl);

        $this->tickets = collect();
    }

    /**
     * @param  ExpoMessage|ExpoMessage[]|Collection<int, ExpoMessage>  $expoMessages
     * @return Collection<int, PushTicketResponse>
     */
    public function notify(ExpoMessage|Collection|array $expoMessages): Collection
    {
        /** @var Collection<int, ExpoMessage> $expoMessages */
        $this->expoMessages = $expoMessages instanceof Collection ? $expoMessages : collect(Arr::wrap($expoMessages));

        return $this->storeNotificationsToSendInTheNextBatch()
            ->prepareNotificationsToSendNow()
            ->sendNotifications();
    }

    /**
     * @param  Collection<int, string>|array  $tokenIds
     * @return Collection<int, PushReceiptResponse>
     *
     * @throws ExpoNotificationsException
     */
    public function receipts(Collection|array $tokenIds): Collection
    {
        if ($tokenIds instanceof Collection) {
            $tokenIds = $tokenIds->toArray();
        }

        $response = $this->http->post('/getReceipts', ['ids' => $tokenIds]);
        if (! $response->successful()) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $data = json_decode($response->body(), true);
        if (! empty($data['errors'])) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        return collect($data['data'])->map(function ($responseItem, $id) {
            $data = (new PushReceiptResponse())
                ->id($id)
                ->status($responseItem['status']);

            if ($responseItem['status'] === ExpoResponseStatus::ERROR->value) {
                $responseItemDetails = is_string($responseItem['details']) ? json_decode($responseItem['details'], true) : $responseItem['details'];

                $data
                    ->message($responseItem['message'])
                    ->details($responseItemDetails);
            }

            return $data;
        });
    }

    public function getNotificationChunks(): Collection
    {
        return $this->notificationChunks ?? collect();
    }

    /**
     * @param  Collection<int, string>  $tokens
     */
    private function checkAndStoreTickets(Collection $tokens): void
    {
        $this->tickets
            ->intersectByKeys($tokens)
            ->each(function (PushTicketResponse $ticket, $index) use ($tokens) {
                if ($ticket->status === ExpoResponseStatus::ERROR->value) {
                    if (
                        is_array($ticket->details) &&
                        array_key_exists('error', $ticket->details) &&
                        $ticket->details['error'] === ExpoResponseStatus::DEVICE_NOT_REGISTERED->value
                    ) {
                        event(new InvalidExpoToken($tokens->get($index)));
                    }
                } else {
                    $this->ticketStorage->store($ticket->ticketId, $tokens->get($index));
                }
            });
    }

    private function storeNotificationsToSendInTheNextBatch(): ExpoNotificationsService
    {
        $this->expoMessages
            ->filter(fn (ExpoMessage $message) => $message->shouldBatch)
            ->each(fn (ExpoMessage $message) => $this->notificationStorage->store($message));

        return $this;
    }

    private function prepareNotificationsToSendNow(): ExpoNotificationsService
    {
        $this->notificationsToSend = $this->expoMessages
            ->reject(fn (ExpoMessage $message) => $message->shouldBatch)
            ->map(fn (ExpoMessage $message) => $message->toExpoData())
            ->values();

        // Splits into multiples chunks of max limitation
        $this->notificationChunks = $this->notificationsToSend->chunk($this->pushNotificationsPerRequestLimit);

        return $this;
    }

    private function sendNotifications(): Collection
    {
        if ($this->notificationsToSend->isEmpty()) {
            return collect();
        }

        $this->notificationChunks
            ->each(
                fn ($chunk, $index) => $this->sendNotificationsChunk($chunk->toArray())
            );

        $this->checkAndStoreTickets($this->notificationsToSend->pluck('to')->flatten());

        return $this->tickets;
    }

    private function handleSendNotificationsResponse(Response $response): void
    {
        $data = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        if (! empty($data['errors'])) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $this->setTicketsFromData($data);
    }

    private function setTicketsFromData(array $data): ExpoNotificationsService
    {
        collect($data['data'])
            ->each(function ($responseItem) {
                if ($responseItem['status'] === ExpoResponseStatus::ERROR->value) {
                    $this->tickets->push(
                        (new PushTicketResponse())
                            ->status($responseItem['status'])
                            ->message($responseItem['message'])
                            ->details($responseItem['details'])
                    );
                } else {
                    $this->tickets->push(
                        (new PushTicketResponse())
                            ->status($responseItem['status'])
                            ->ticketId($responseItem['id'])
                    );
                }
            });

        return $this;
    }

    private function sendNotificationsChunk(array $chunk)
    {
        $response = $this->http->post(self::SEND_NOTIFICATION_ENDPOINT, $chunk);

        if (! $response->successful()) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $this->handleSendNotificationsResponse($response);
    }
}
