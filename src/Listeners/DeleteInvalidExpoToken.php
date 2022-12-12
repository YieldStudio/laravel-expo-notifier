<?php

namespace YieldStudio\LaravelExpoNotifier\Listeners;

use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Events\InvalidExpoToken;

class DeleteInvalidExpoToken
{
    public function handle(InvalidExpoToken $event, ExpoTokenStorageInterface $tokenStorage): void
    {
        $tokenStorage->delete($event->token);
    }
}
