<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Commands;

use Illuminate\Console\Command;
use YieldStudio\LaravelExpoNotifier\Jobs\SendExpoDelayedNotifications;

final class ExpoDelayedNotificationsSend extends Command
{
    protected $signature = 'expo:notifications:send';

    protected $description = 'Send delayed notifications';

    public function handle(): void
    {
        SendExpoDelayedNotifications::dispatchSync();
    }
}
