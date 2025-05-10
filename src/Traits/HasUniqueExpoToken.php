<?php

declare(strict_types = 1);

namespace YieldStudio\LaravelExpoNotifier\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

trait HasUniqueExpoToken
{
    public function expoTokens(): MorphOne
    {
        return $this->morphOne(ExpoToken::class, 'owner');
    }

    public function expoNotifications(): MorphMany
    {
        return $this->morphMany(ExpoNotification::class, 'receiver');
    }
}