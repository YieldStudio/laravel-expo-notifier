<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications\Commands;

use YieldStudio\LaravelExpoNotifications\Jobs\SendExpoDelayedNotifications;
use Illuminate\Console\Command;

final class ExpoDelayedNotificationsSend extends Command
{
    protected $signature = 'expo:notifications:send';

    protected $description = 'Send delayed notifications';

    public function handle(): void
    {
        SendExpoDelayedNotifications::dispatchSync();
    }
}
