<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Commands;

use Illuminate\Console\Command;
use YieldStudio\LaravelExpoNotifier\Jobs\CheckTickets as CheckTicketsJob;

final class CheckTickets extends Command
{
    protected $signature = 'expo:tickets:check';

    protected $description = 'Purge not registered device tokens';

    public function handle(): void
    {
        CheckTicketsJob::dispatchSync();
    }
}
