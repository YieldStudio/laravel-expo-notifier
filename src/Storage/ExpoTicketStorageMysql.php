<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Storage;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoTicket as ExpoTicketDto;
use YieldStudio\LaravelExpoNotifier\Models\ExpoTicket;

class ExpoTicketStorageMysql implements ExpoTicketStorageInterface
{
    public function retrieve(int $amount = 1000): Collection
    {
        return ExpoTicket::query()
            ->take($amount)
            ->get()
            ->map(fn ($ticket) => ExpoTicketDto::make($ticket->ticket_id, $ticket->token));
    }

    public function store(string $ticketId, string $token): ExpoTicketDto
    {
        $expoTicket = ExpoTicket::firstOrCreate([
            'ticket_id' => $ticketId,
        ], [
            'ticket_id' => $ticketId,
            'token' => $token,
        ]);

        return ExpoTicketDto::make($expoTicket->ticket_id, $expoTicket->token);
    }

    public function delete(string|array $ticketIds): void
    {
        $ticketIds = Arr::wrap($ticketIds);
        ExpoTicket::query()->whereIn('ticket_id', $ticketIds)->delete();
    }

    public function count(): int
    {
        return ExpoTicket::query()->count();
    }
}
