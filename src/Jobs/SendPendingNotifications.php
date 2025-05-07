<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoNotificationsServiceInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoNotification;

class SendPendingNotifications
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function handle(
        ExpoNotificationsServiceInterface $expoNotificationsService,
        ExpoPendingNotificationStorageInterface $expoNotification,
    ): void {
        $sent = collect();

        while ($expoNotification->count() > 0) {
            $notifications = $expoNotification
                ->retrieve()
                // Avoid double sending in case of deletion error.
                ->reject(fn (ExpoNotification $notification) => $sent->contains($notification->id));

            if ($notifications->isEmpty()) {
                break;
            }

            $expoMessages = $notifications->pluck('message');
            $ids = $notifications->pluck('id');

            $expoNotificationsService->notify($expoMessages);

            $sent = $sent->merge($ids);
            $expoNotification->updateSent($ids->toArray());
        }
    }
}
