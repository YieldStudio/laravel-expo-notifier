<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Commands;

use Illuminate\Console\Command;
use YieldStudio\LaravelExpoNotifier\Jobs\ClearSentNotifications as ClearSentNotificationsJob;

final class ClearSentNotifications extends Command
{
    protected $signature = 'expo:notifications:clear';

    protected $description = 'Clear sent notifications';

    public function handle(): void
    {
        ClearSentNotificationsJob::dispatchSync();
    }
}
