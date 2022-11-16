<?php

namespace YieldStudio\LaravelExpoNotifications\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use YieldStudio\LaravelExpoNotifications\ExpoNotificationsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ExpoNotificationsServiceProvider::class];
    }
}
