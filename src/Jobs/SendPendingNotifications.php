<?php

namespace YieldStudio\LaravelExpoNotifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Services\ExpoNotificationsService;

class SendPendingNotifications
{
    use Dispatchable;
    use SerializesModels;
    use Queueable;

    public function handle(
        ExpoNotificationsService                $expoNotificationsService,
        ExpoPendingNotificationStorageInterface $expoNotification,
    ): void
    {
        while ($expoNotification->count() > 0) {
            $notifications = $expoNotification->retrieve();

            $expoMessages = $notifications->pluck('message');

            $response = $expoNotificationsService->notify(
                $expoMessages->map(function (ExpoMessage $expoMessage) {
                    return $expoMessage->toExpoData();
                })->toArray()
            );
            $expoNotification->delete($notifications->pluck('id')->toArray());

            $tokens = $expoMessages->pluck('to')->flatten();

            $expoNotificationsService->storeTicketsFromResponse($tokens, $response);
        }
    }
}
