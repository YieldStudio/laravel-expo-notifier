<?php

namespace YieldStudio\LaravelExpoNotifier\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Services\ExpoNotificationsService;

class SendExpoDelayedNotifications
{
    use Dispatchable;
    use SerializesModels;
    public const ERROR = 'error';
    public const DEVICE_NOT_REGISTERED = 'DeviceNotRegistered';

    public function handle(
        ExpoNotificationsService                $expoNotificationsService,
        ExpoPendingNotificationStorageInterface $expoNotification,
        ExpoTokenStorageInterface               $tokenStorage,
        ExpoTicketStorageInterface              $ticketStorage
    ): void {
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
                        'token' => $tokens->get($index),
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
