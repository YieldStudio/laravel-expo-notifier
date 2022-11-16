<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services;

use Illuminate\Support\Facades\Http;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;

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

    public function notify(array $expoMessages)
    {
        $response = $this->http->post('/send', $expoMessages);

        if (! $response->successful()) {
            throw new ExpoNotificationsException('ExpoNotificationsService:push() failed', $response->status());
        }

        return json_decode($response->body(), true);
    }

    public function receipts(array $tokenIds)
    {
        $response = $this->http->post('/getReceipts', ['ids' => $tokenIds]);

        if (! $response->successful()) {
            throw new ExpoNotificationsException('ExpoNotificationsService:receipts() failed', $response->status());
        }

        return json_decode($response->body(), true);
    }
}
