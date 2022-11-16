<?php

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
