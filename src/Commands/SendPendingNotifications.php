<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Commands;

use Illuminate\Console\Command;
use YieldStudio\LaravelExpoNotifier\Jobs\SendPendingNotifications as SendPendingNotificationsJob;

final class SendPendingNotifications extends Command
{
    protected $signature = 'expo:notifications:send';

    protected $description = 'Send pending notifications';

    public function handle(): void
    {
        SendPendingNotificationsJob::dispatchSync();
    }
}
