<?php

namespace YieldStudio\LaravelExpoNotifications\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use YieldStudio\LaravelExpoNotifications\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifications\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifications\Services\ExpoNotificationsService;

class CheckExpoPushTickets
{
    const OK = 'ok';
    const ERROR = "error";

    use Dispatchable, SerializesModels;

    public function handle(
        ExpoNotificationsService   $expoNotificationsService,
        ExpoTicketStorageInterface $ticketStorage,
        ExpoTokenStorageInterface $tokenStorage
    ): void
    {
        while ($ticketStorage->total() > 0) {
            $tickets = $ticketStorage->retrieve();

            $ticketIds = $tickets->pluck('ticket_id')->toArray();

            $response = $expoNotificationsService->receipts($ticketIds);

            $responseData = collect($response['data']);

            if ($responseData->count() === 0) {
                break;
            }

            $tokensToDelete = [];
            $ticketsToDelete = [];
            $tickets->map(function ($ticket) use ($responseData, $ticketStorage, $tokenStorage, &$tokensToDelete, &$ticketsToDelete) {
                $ticketResponse = $responseData->get($ticket->ticket_id);
                if (in_array($ticketResponse['status'], [self::OK, self::ERROR])) {
                    if ($ticketResponse['status'] === self::ERROR) {
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
