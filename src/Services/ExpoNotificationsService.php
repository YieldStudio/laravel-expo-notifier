<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Services\Dto\PushReceiptResponse;
use YieldStudio\LaravelExpoNotifier\Services\Dto\PushTicketResponse;

final class ExpoNotificationsService
{
    public function __construct(
        string $apiUrl,
        string $host,
        protected readonly ExpoTokenStorageInterface $tokenStorage,
        protected readonly ExpoTicketStorageInterface $ticketStorage
    ) {
        $this->http = Http::withHeaders([
            'host' => $host,
            'accept' => 'application/json',
            'accept-encoding' => 'gzip, deflate',
            'content-type' => 'application/json',
        ])->baseUrl($apiUrl);
    }

    public function notify(ExpoMessage|array $expoMessages): Collection
    {
        if ($expoMessages instanceof ExpoMessage) {
            $expoMessages = [$expoMessages];
        }

        $response = $this->http->post('/send', $expoMessages);

        if (! $response->successful()) {
            throw new ExpoNotificationsException('ExpoNotificationsService:push() failed', $response->status());
        }

        $responseData = json_decode($response->body(), true);

        if (! empty($responseData['errors'])) {
            throw new ExpoNotificationsException('ExpoNotificationsService:push() failed', $response->status());
        }

        return collect($responseData['data'])->map(function ($responseItem) {
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

    public function receipts(array $tokenIds): Collection
    {
        $response = $this->http->post('/getReceipts', ['ids' => $tokenIds]);

        if (! $response->successful()) {
            throw new ExpoNotificationsException('ExpoNotificationsService:receipts() failed', $response->status());
        }

        $responseData = json_decode($response->body(), true);

        if (! empty($responseData['errors'])) {
            throw new ExpoNotificationsException('ExpoNotificationsService:push() failed', $response->status());
        }

        return collect($responseData['data'])->map(function ($responseItem, $id) {
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

    public function storeTicketsFromResponse(Collection $tokens, Collection $response): void
    {
        $tokensToDelete = [];
        $response
            ->intersectByKeys($tokens)
            ->each(function (PushTicketResponse $tokenResponse, $index) use ($tokens, &$tokensToDelete) {
                if ($tokenResponse->status === ExpoResponseStatus::ERROR->value && $tokenResponse->details['error'] === ExpoResponseStatus::DEVICE_NOT_REGISTERED->value) {
                    $tokensToDelete[] = $tokens->get($index);
                } else {
                    $this->ticketStorage->store($tokenResponse->ticketId, $tokens->get($index));
                }
            });
        $this->tokenStorage->delete($tokensToDelete);
    }

    public function checkNotifyResponse(Collection $tickets, Collection $response): void
    {
        $tokensToDelete = [];
        $ticketsToDelete = [];
        $tickets->map(function ($ticket) use ($response, &$tokensToDelete, &$ticketsToDelete) {
            /** @var PushTicketResponse $ticketResponse */
            $ticketResponse = $response->get($ticket->id);
            if (in_array($ticketResponse->status, [ExpoResponseStatus::OK->value, ExpoResponseStatus::ERROR->value])) {
                if ($ticketResponse->status === ExpoResponseStatus::ERROR->value) {
                    $tokensToDelete[] = $ticket->token;
                }
                $ticketsToDelete[] = $ticket->id;
            }
        });

        $this->tokenStorage->delete($tokensToDelete);
        $this->ticketStorage->delete($ticketsToDelete);
    }
}
