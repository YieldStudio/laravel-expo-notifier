<?php

namespace YieldStudio\LaravelExpoNotifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class ExpoToken extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
