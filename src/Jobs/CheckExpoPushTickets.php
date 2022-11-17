<?php

namespace YieldStudio\LaravelExpoNotifier\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Enums\ExpoResponseStatus;
use YieldStudio\LaravelExpoNotifier\Services\Dto\PushTicketResponse;
use YieldStudio\LaravelExpoNotifier\Services\ExpoNotificationsService;

class CheckExpoPushTickets
{
    use Dispatchable;
    use SerializesModels;

    public function handle(
        ExpoNotificationsService   $expoNotificationsService,
        ExpoTicketStorageInterface $ticketStorage,
        ExpoTokenStorageInterface $tokenStorage
    ): void {
        while ($ticketStorage->total() > 0) {
            $tickets = $ticketStorage->retrieve();

            $ticketIds = $tickets->pluck('ticket_id')->toArray();

            $response = $expoNotificationsService->receipts($ticketIds);

            if ($response->count() === 0) {
                break;
            }

            $tokensToDelete = [];
            $ticketsToDelete = [];
            $tickets->map(function ($ticket) use ($response, $ticketStorage, $tokenStorage, &$tokensToDelete, &$ticketsToDelete) {
                /** @var PushTicketResponse $ticketResponse */
                $ticketResponse = $response->get($ticket->ticket_id);
                if (in_array($ticketResponse->status, [ExpoResponseStatus::OK->value, ExpoResponseStatus::ERROR->value])) {
                    if ($ticketResponse->status === ExpoResponseStatus::ERROR->value) {
                        $tokensToDelete[] = $ticket->token;
                    }
                    $ticketsToDelete[] = $ticket->id;
                }
            });

            $tokenStorage->delete($tokensToDelete);
            $ticketStorage->delete($ticketsToDelete);
        }
    }
}
