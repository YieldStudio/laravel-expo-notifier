<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

interface ExpoTicketStorageInterface
{
    public function store(string $ticketId, string $token);

    public function delete(array $ticketIds);

    public function retrieve(int $amount = 1000);

    public function count();
}
