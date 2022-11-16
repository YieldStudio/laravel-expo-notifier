<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExpoTicket extends Model
{
    protected $guarded = ['id'];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(ExpoNotification::class);
    }

}