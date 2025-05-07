<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;

class ClearSentNotifications
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function handle(
        ExpoPendingNotificationStorageInterface $expoNotification,
    ): void {
        while (($sentNotifications = $expoNotification->retrieve(sent: true))->isNotEmpty()) {
            $expoNotification->delete($sentNotifications->pluck('id')->toArray());
        }
    }
}
