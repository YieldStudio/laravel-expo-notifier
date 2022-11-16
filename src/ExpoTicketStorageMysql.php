<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Database\Eloquent\Collection;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Models\ExpoTicket;

final class ExpoTicketStorageMysql implements ExpoTicketStorageInterface
{
    public function getByKey(string $key): ?ExpoTicket
    {
        return ExpoTicket::where('key', '=', $key)
            ->first();
    }

    public function getByValue(string $value): ?ExpoTicket
    {
        return ExpoTicket::where('value', '=', $value)
            ->first();
    }

    public function retrieve(int $amount = 1000): Collection
    {
        return ExpoTicket::take($amount)->get();
    }

    public function store(string $ticketId, string $token): ExpoTicket
    {
        return ExpoTicket::create([
            'ticket_id' => $ticketId,
            'token' => $token
        ]);
    }

    public function delete(array $ids): bool
    {
        return (bool) ExpoTicket::whereIn('id', $ids)->delete();
    }

    public function total(): int
    {
        return ExpoTicket::count();
    }
}