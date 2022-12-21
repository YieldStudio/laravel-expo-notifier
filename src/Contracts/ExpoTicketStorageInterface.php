<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoTicket;

interface ExpoTicketStorageInterface
{
    public function store(string $ticketId, string $token): ExpoTicket;

    /**
     * @param  int  $amount
     * @return Collection<int, ExpoTicket>
     */
    public function retrieve(int $amount = 1000): Collection;

    public function delete(string|array $ticketIds): void;

    public function count(): int;
}
