<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;
use YieldStudio\LaravelExpoNotifier\Services\Dto\PushReceiptResponse;
use YieldStudio\LaravelExpoNotifier\Services\Dto\PushTicketResponse;

final class ExpoNotificationsService
{
    protected string $apiUrl = 'https://exp.host/--/api/v2/push';

    public function __construct()
    {
        $this->http = Http::withHeaders([
            'host' => 'exp.host',
            'accept' => 'application/json',
            'accept-encoding' => 'gzip, deflate',
            'content-type' => 'application/json',
        ])->baseUrl($this->apiUrl);
    }

    public function notify(array $expoMessages): Collection
    {
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
                    ->id($responseItem['id']);
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
}
