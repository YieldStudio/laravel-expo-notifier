<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications\Commands;

use YieldStudio\LaravelExpoNotifications\Jobs\CheckExpoPushTickets;
use Illuminate\Console\Command;

final class ExpoTicketsPurge extends Command
{
    protected $signature = 'expo:purge-tickets';

    protected $description = 'Purge not registered device tokens';

    public function handle(): void
    {
        CheckExpoPushTickets::dispatchSync();
    }
}
