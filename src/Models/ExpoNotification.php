<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExpoNotification extends Model
{
    protected $guarded = ['id'];

    public function receiver(): MorphTo
    {
        return $this->morphTo();
    }
}
