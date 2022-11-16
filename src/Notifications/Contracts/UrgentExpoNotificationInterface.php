<?php

namespace YieldStudio\LaravelExpoNotifications\Notifications\Contracts;

interface UrgentExpoNotificationInterface
{
    public function isUrgent(): bool;
}