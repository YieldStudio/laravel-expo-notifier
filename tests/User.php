<?php

namespace YieldStudio\LaravelExpoNotifications\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model {
    use Notifiable;
}
