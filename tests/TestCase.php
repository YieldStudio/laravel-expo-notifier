<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Tests;

use Faker\Factory;
use Faker\Generator;
use Orchestra\Testbench\TestCase as Orchestra;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoNotificationsServiceInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsServiceProvider;
use YieldStudio\LaravelExpoNotifier\FakeExpoNotificationsService;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoPendingNotificationStorageMysql;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoTicketStorageMysql;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoTokenStorageMysql;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ExpoNotificationsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(ExpoTokenStorageInterface::class, config('expo-notifications.drivers.token'));
        $this->app->bind(ExpoTicketStorageInterface::class, config('expo-notifications.drivers.ticket'));
        $this->app->bind(ExpoPendingNotificationStorageInterface::class, config('expo-notifications.drivers.notification'));

        $this->app->bind(ExpoNotificationsServiceInterface::class, function ($app) {
            return new FakeExpoNotificationsService(
                'http://localhost', // won't be used, just here to respect the contract
                'localhost', // won't be used, just here to respect the contract
                $app->make(ExpoPendingNotificationStorageInterface::class),
                $app->make(ExpoTicketStorageInterface::class)
            );
        });
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        config()->set('database.default', 'testbench');
        config()->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Setup queue database connections.
        config()->set('queue.batching.database', 'testbench');
        config()->set('queue.failed.database', 'testbench');

        // Setup Expo configuration
        config()->set('expo-notifications.drivers', [
            'token' => ExpoTokenStorageMysql::class,
            'ticket' => ExpoTicketStorageMysql::class,
            'notification' => ExpoPendingNotificationStorageMysql::class,
        ]);
    }

    protected function fake(): Generator
    {
        return Factory::create();
    }
}
