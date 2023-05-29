<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Dto\PushReceiptResponse;
use YieldStudio\LaravelExpoNotifier\Dto\PushTicketResponse;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Events\InvalidExpoToken;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;

final class ExpoNotificationsService
{
    private PendingRequest $http;

    public function __construct(
        string $apiUrl,
        string $host,
        protected readonly ExpoPendingNotificationStorageInterface $notificationStorage,
        protected readonly ExpoTicketStorageInterface $ticketStorage
    ) {
        $this->http = Http::withHeaders([
            'host' => $host,
            'accept' => 'application/json',
            'accept-encoding' => 'gzip, deflate',
            'content-type' => 'application/json',
        ])->baseUrl($apiUrl);
    }

    /**
     * @param  ExpoMessage|ExpoMessage[]|Collection<int, ExpoMessage>  $expoMessages
     * @return Collection<int, PushTicketResponse>
     *
     * @throws ExpoNotificationsException
     */
    public function notify(ExpoMessage|Collection|array $expoMessages): Collection
    {
        /** @var Collection<int, ExpoMessage> $expoMessages */
        $expoMessages = $expoMessages instanceof Collection ? $expoMessages : collect(Arr::wrap($expoMessages));

        $shouldBatchFilter = fn (ExpoMessage $message) => $message->shouldBatch;

        // Store notifications to send in the next batch
        $expoMessages
            ->filter($shouldBatchFilter)
            ->each(fn (ExpoMessage $message) => $this->notificationStorage->store($message));

        // Filter notifications to send now
        $toSend = $expoMessages
            ->reject($shouldBatchFilter)
            ->map(fn (ExpoMessage $message) => $message->toExpoData())
            ->values();

        if ($toSend->isEmpty()) {
            return collect();
        }

        $response = $this->http->post('/send', $toSend->toArray());
        if (! $response->successful()) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $data = json_decode($response->body(), true);
        if (! empty($data['errors'])) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $tickets = collect($data['data'])->map(function ($responseItem) {
            if ($responseItem['status'] === ExpoResponseStatus::ERROR->value) {
                return (new PushTicketResponse())
                    ->status($responseItem['status'])
                    ->message($responseItem['message'])
                    ->details($responseItem['details']);
            }

            return (new PushTicketResponse())
                ->status($responseItem['status'])
                ->ticketId($responseItem['id']);
        });

        $this->checkAndStoreTickets($toSend->pluck('to')->flatten(), $tickets);

        return $tickets;
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
                $data
                    ->message($responseItem['message'])
                    ->details(json_decode($responseItem['details'], true));
            }

            return $data;
        });
    }

    /**
     * @param  Collection<int, string>  $tokens
     * @param  Collection<int, PushTicketResponse>  $tickets
     */
    private function checkAndStoreTickets(Collection $tokens, Collection $tickets): void
    {
        $tickets
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
