<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoTicket;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Events\InvalidExpoToken;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsService;

class CheckTickets
{
    use Dispatchable;
    use SerializesModels;
    use Queueable;

    public function handle(
        ExpoNotificationsService $expoNotificationsService,
        ExpoTicketStorageInterface $ticketStorage
    ): void {
        while ($ticketStorage->count() > 0) {
            $tickets = $ticketStorage->retrieve();
            $ticketIds = $tickets->pluck('id')->toArray();

            $response = $expoNotificationsService->receipts($ticketIds);
            if ($response->isEmpty()) {
                break;
            }

            $this->check($ticketStorage, $tickets, $response);
        }
    }

    protected function check(ExpoTicketStorageInterface $ticketStorage, Collection $tickets, Collection $receipts): void
    {
        $ticketsToDelete = [];

        $tickets->each(function (ExpoTicket $ticket) use ($receipts, &$ticketsToDelete) {
            $receipt = $receipts->get($ticket->id);

            if (! is_null($receipt) && in_array($receipt->status, [ExpoResponseStatus::OK->value, ExpoResponseStatus::ERROR->value])) {
                if (
                    is_array($receipt->details) &&
                    array_key_exists('error', $receipt->details) &&
                    $receipt->details['error'] === ExpoResponseStatus::DEVICE_NOT_REGISTERED->value
                ) {
                    event(new InvalidExpoToken($ticket->token));

                    return;
                }

                $ticketsToDelete[] = $ticket->id;
            }
        });

        $ticketStorage->delete($ticketsToDelete);
    }
}
