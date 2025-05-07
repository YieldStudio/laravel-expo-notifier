<?php

declare(strict_types = 1);

namespace YieldStudio\LaravelExpoNotifier\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

trait HasUniqueExpoToken
{
    public function expoTokens(): MorphOne
    {
        return $this->morphOne(ExpoToken::class, 'owner');
    }
}