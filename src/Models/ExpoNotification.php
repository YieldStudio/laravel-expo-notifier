<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class ExpoNotification extends Model
{
    protected $guarded = ['id'];

    public function scopeUnsent(Builder $query): Builder
    {
        return $query->whereNull('sent_at');
    }
}