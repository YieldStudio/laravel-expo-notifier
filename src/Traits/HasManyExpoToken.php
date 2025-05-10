<?php

declare(strict_types = 1);

namespace YieldStudio\LaravelExpoNotifier\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

trait HasManyExpoToken
{
    public function expoTokens(): MorphMany
    {
        return $this->morphMany(ExpoToken::class, 'owner');
    }

    public function expoNotifications(): MorphMany
    {
        return $this->morphMany(ExpoNotification::class, 'receiver');
    }
}