<?php

namespace YieldStudio\LaravelExpoNotifier\Events;

final class InvalidExpoToken
{
    public function __construct(public readonly string $token)
    {
    }
}
