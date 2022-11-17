<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Enums;

enum ExpoResponseStatus: string
{
    case OK = 'ok';
    case ERROR = 'error';
}
