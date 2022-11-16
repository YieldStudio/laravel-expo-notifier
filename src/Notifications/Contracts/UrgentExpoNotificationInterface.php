<?php

namespace YieldStudio\LaravelExpoNotifier\Notifications\Contracts;

interface UrgentExpoNotificationInterface
{
    public function isUrgent(): bool;
}