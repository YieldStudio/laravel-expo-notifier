<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ExpoNotificationsServiceProvider::class];
    }
}
