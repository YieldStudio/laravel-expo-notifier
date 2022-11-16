<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Commands;

use Illuminate\Console\Command;
use YieldStudio\LaravelExpoNotifier\Jobs\CheckExpoPushTickets;

final class ExpoTicketsPurge extends Command
{
    protected $signature = 'expo:purge-tickets';

    protected $description = 'Purge not registered device tokens';

    public function handle(): void
    {
        CheckExpoPushTickets::dispatchSync();
    }
}
