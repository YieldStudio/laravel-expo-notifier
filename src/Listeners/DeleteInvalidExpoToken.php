<?php

namespace YieldStudio\LaravelExpoNotifier\Listeners;

use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Events\InvalidExpoToken;

class DeleteInvalidExpoToken
{
    public function __construct(protected readonly ExpoTokenStorageInterface $tokenStorage)
    {
    }

    public function handle(InvalidExpoToken $event): void
    {
        $this->tokenStorage->delete($event->token);
    }
}
