<?php

namespace YieldStudio\LaravelExpoNotifications\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifications\Contracts\NotificationInterface;
use YieldStudio\LaravelExpoNotifications\Contracts\TicketStorageInterface;
use YieldStudio\LaravelExpoNotifications\Contracts\TokenStorageInterface;
use YieldStudio\LaravelExpoNotifications\Services\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifications\Services\ExpoNotificationsService;

class SendExpoDelayedNotifications
{
    const ERROR = 'error';
    const DEVICE_NOT_REGISTERED = 'DeviceNotRegistered';

    use Dispatchable, SerializesModels;

    public function handle(
        ExpoNotificationsService $expoNotificationsService,
        NotificationInterface    $expoNotification,
        TokenStorageInterface    $tokenStorage,
        TicketStorageInterface   $ticketStorage
    ): void
    {
        while ($expoNotification->total() > 0) {
            $notifications = $expoNotification->retrieve();

            $expoMessages = $notifications->pluck('message');

            $response = $expoNotificationsService->notify(
                $expoMessages->map(function (ExpoMessage $expoMessage) {
                    return $expoMessage->toExpoData();
                })->toArray()
            );
            $expoNotification->delete($notifications->pluck('id')->toArray());

            $tokens = $expoMessages->pluck('to')->flatten();

            $tokensToDelete = [];
            collect($response['data'])
                ->intersectByKeys($tokens)
                ->transform(function ($data, $index) use ($tokens) {
                    return [
                        ...$data,
                        'token' => $tokens->get($index)
                    ];
                })->each(function ($tokenResponse) use ($tokenStorage, $ticketStorage, &$tokensToDelete) {
                    if ($tokenResponse['status'] === self::ERROR && $tokenResponse['details']['error'] === self::DEVICE_NOT_REGISTERED) {
                        $tokensToDelete[] = $tokenResponse['token'];
                    } else {
                        $ticketStorage->store($tokenResponse['id'], $tokenResponse['token']);
                    }
                });
            $tokenStorage->delete($tokensToDelete);
        }
    }
}
