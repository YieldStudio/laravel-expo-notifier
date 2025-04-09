<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoNotificationsServiceInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Dto\PushTicketResponse;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Events\InvalidExpoToken;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;

/**
 * Fake Expo Notification service to avoid real API calls while coding the feature.
 * If you want to tests against the real API, you can swap the ExpoNotificationsServiceInterface binding in TestCase
 */
final class FakeExpoNotificationsService implements ExpoNotificationsServiceInterface
{
    public const PUSH_NOTIFICATIONS_PER_REQUEST_LIMIT = 100;

    private ?Collection $expoMessages;

    private ?Collection $notificationsToSend;

    private Collection $tickets;

    private ?Collection $notificationChunks;

    public function __construct(
        string $apiUrl,
        string $host,
        ?string $accessToken,
        /* @phpstan-ignore-next-line */
        private readonly ExpoPendingNotificationStorageInterface $notificationStorage,
        /* @phpstan-ignore-next-line */
        private readonly ExpoTicketStorageInterface $ticketStorage
    ) {
        $this->tickets = collect();
    }

    public function notify(ExpoMessage|Collection|array $expoMessages): Collection
    {
        /** @var Collection<int, ExpoMessage> $expoMessages */
        $this->expoMessages = $expoMessages instanceof Collection ? $expoMessages : collect(Arr::wrap($expoMessages));

        return $this->storeNotificationsToSendInTheNextBatch()
            ->prepareNotificationsToSendNow()
            ->sendNotifications();
    }

    public function receipts(array|Collection $tokenIds): Collection
    {
        // TODO: Implement receipts() method.
    }

    public function getNotificationChunks(): Collection
    {
        return $this->notificationChunks ?? collect();
    }

    private function prepareNotificationsToSendNow(): FakeExpoNotificationsService
    {
        $this->notificationsToSend = $this->expoMessages
            ->reject(fn (ExpoMessage $message) => $message->shouldBatch)
            ->map(fn (ExpoMessage $message) => $message->toExpoData())
            ->values();

        // Splits into multiples chunks of max limitation
        $this->notificationChunks = $this->notificationsToSend->chunk(self::PUSH_NOTIFICATIONS_PER_REQUEST_LIMIT);

        return $this;
    }

    private function storeNotificationsToSendInTheNextBatch(): FakeExpoNotificationsService
    {
        $this->expoMessages
            ->filter(fn (ExpoMessage $message) => $message->shouldBatch)
            ->each(fn (ExpoMessage $message) => $this->notificationStorage->store($message));

        return $this;
    }

    private function sendNotifications(): Collection
    {
        if ($this->notificationsToSend->isEmpty()) {
            return collect();
        }

        $this->notificationChunks
            ->each(
                fn ($chunk, $index) => $this->sendNotificationsChunk($chunk->toArray(), $index)
            );

        $this->checkAndStoreTickets($this->notificationsToSend->pluck('to')->flatten());

        return $this->tickets;
    }

    private function sendNotificationsChunk(array $chunk, int $chunkId): void
    {
        $data = [];
        foreach ($chunk as $notification) {
            $data[] = [
                'id' => Str::orderedUuid()->toString(),
                'status' => ExpoResponseStatus::OK->value,
                '__notification' => $notification,
            ];
        }

        $response = Http::fake([
            'api-push/'.$chunkId => Http::response([
                'data' => $data,
            ]),
        ])->get('/api-push/'.$chunkId);

        if (! $response->successful()) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $this->handleSendNotificationsResponse($response);
    }

    private function handleSendNotificationsResponse(Response $response): void
    {
        $data = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        if (! empty($data['errors'])) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $this->setTicketsFromData($data);
    }

    private function setTicketsFromData(array $data): FakeExpoNotificationsService
    {
        collect($data['data'])
            ->each(function ($responseItem) {
                if ($responseItem['status'] === ExpoResponseStatus::ERROR->value) {
                    $this->tickets->push(
                        (new PushTicketResponse)
                            ->status($responseItem['status'])
                            ->message($responseItem['message'])
                            ->details($responseItem['details'])
                    );
                } else {
                    $this->tickets->push(
                        (new PushTicketResponse)
                            ->status($responseItem['status'])
                            ->ticketId($responseItem['id'])
                    );
                }
            });

        return $this;
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
}
