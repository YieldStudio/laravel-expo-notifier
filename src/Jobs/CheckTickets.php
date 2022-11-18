<?php

namespace YieldStudio\LaravelExpoNotifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsService;

class CheckTickets
{
    use Dispatchable;
    use SerializesModels;
    use Queueable;

    public function handle(
        ExpoNotificationsService   $expoNotificationsService,
        ExpoTicketStorageInterface $ticketStorage
    ): void {
        while ($ticketStorage->count() > 0) {
            $tickets = $ticketStorage->retrieve();
            $ticketIds = $tickets->pluck('id')->toArray();

            $response = $expoNotificationsService->receipts($ticketIds);
            if ($response->isEmpty()) {
                break;
            }

            $expoNotificationsService->checkNotifyResponse($tickets, $response);
        }
    }
}
