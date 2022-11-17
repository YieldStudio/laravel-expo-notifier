<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Models\ExpoTicket;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoTicket as ExpoTicketDto;

class ExpoTicketStorageMysql implements ExpoTicketStorageInterface
{
    /**
     * @param int $amount
     * @return Collection<string, ExpoTicketDto>
     */
    public function retrieve(int $amount = 1000): Collection
    {
        return ExpoTicket::take($amount)
            ->get()
            ->map(fn($ticket) => ExpoTicketDto::make($ticket->ticket_id, $ticket->token));
    }

    public function store(string $ticketId, string $token): ExpoTicketDto
    {
        $expoTicket = ExpoTicket::create([
            'ticket_id' => $ticketId,
            'token' => $token,
        ]);

        return ExpoTicketDto::make($expoTicket->ticket_id, $expoTicket->token);
    }

    public function delete(array $ticketIds): void
    {
        ExpoTicket::whereIn('ticket_id', $ticketIds)->delete();
    }

    public function count(): int
    {
        return ExpoTicket::count();
    }
}
