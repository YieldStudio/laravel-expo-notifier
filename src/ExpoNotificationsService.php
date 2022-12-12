<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoTicket;
use YieldStudio\LaravelExpoNotifier\Dto\PushReceiptResponse;
use YieldStudio\LaravelExpoNotifier\Dto\PushTicketResponse;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;

final class ExpoNotificationsService
{
    private PendingRequest $http;

    public function __construct(
        string                                        $apiUrl,
        string                                        $host,
        protected readonly ExpoTokenStorageInterface  $tokenStorage,
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
     * @param ExpoMessage|ExpoMessage[] $expoMessages
     * @return Collection<int, PushTicketResponse>
     * @throws ExpoNotificationsException
     */
    public function notify(ExpoMessage|array $expoMessages): Collection
    {
        if ($expoMessages instanceof ExpoMessage) {
            $expoMessages = [$expoMessages];
        }

        $response = $this->http->post('/send', array_map(fn ($item) => $item->toExpoData(), $expoMessages));
        if (! $response->successful()) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        $data = json_decode($response->body(), true);
        if (! empty($data['errors'])) {
            throw new ExpoNotificationsException($response->toPsrResponse());
        }

        return collect($data['data'])->map(function ($responseItem) {
            if ($responseItem['status'] === ExpoResponseStatus::ERROR->value) {
                $data = (new PushTicketResponse())
                    ->status($responseItem['status'])
                    ->message($responseItem['message'])
                    ->details($responseItem['details']);
            } else {
                $data = (new PushTicketResponse())
                    ->status($responseItem['status'])
                    ->ticketId($responseItem['id']);
            }

            return $data;
        });
    }

    /**
     * @param array $tokenIds
     * @return Collection<int, PushReceiptResponse>
     * @throws ExpoNotificationsException
     */
    public function receipts(array $tokenIds): Collection
    {
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
     * @param Collection<int, string> $tokens
     * @param Collection<int, PushTicketResponse> $tickets
     * @return void
     */
    public function storeTicketsFromResponse(Collection $tokens, Collection $tickets): void
    {
        $tokensToDelete = [];

        $tickets
            ->intersectByKeys($tokens)
            ->each(function (PushTicketResponse $ticket, $index) use ($tokens, &$tokensToDelete) {
                if ($ticket->status === ExpoResponseStatus::ERROR->value && $ticket->details['error'] === ExpoResponseStatus::DEVICE_NOT_REGISTERED->value) {
                    $tokensToDelete[] = $tokens->get($index);
                } else {
                    $this->ticketStorage->store($ticket->ticketId, $tokens->get($index));
                }
            });

        $this->tokenStorage->delete($tokensToDelete);
    }

    /**
     * @param Collection<int, ExpoTicket> $tickets
     * @param Collection<int, PushReceiptResponse> $receipts
     * @return void
     */
    public function checkNotifyResponse(Collection $tickets, Collection $receipts): void
    {
        $tokensToDelete = [];
        $ticketsToDelete = [];

        $tickets->map(function (ExpoTicket $ticket) use ($receipts, &$tokensToDelete, &$ticketsToDelete) {
            $receipt = $receipts->get($ticket->id);
            if (in_array($receipt->status, [ExpoResponseStatus::OK->value, ExpoResponseStatus::ERROR->value])) {
                if ($receipt->status === ExpoResponseStatus::ERROR->value) {
                    $tokensToDelete[] = $ticket->token;
                }
                $ticketsToDelete[] = $ticket->id;
            }
        });

        $this->tokenStorage->delete($tokensToDelete);
        $this->ticketStorage->delete($ticketsToDelete);
    }
}
