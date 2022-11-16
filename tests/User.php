<?php

namespace YieldStudio\LaravelExpoNotifier\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model {
    use Notifiable;
}
