<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications;

use YieldStudio\LaravelExpoNotifications\Commands\ExpoDelayedNotificationsSend;
use YieldStudio\LaravelExpoNotifications\Commands\ExpoTicketsPurge;
use YieldStudio\LaravelExpoNotifications\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifications\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifications\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifications\Services\ExpoNotificationsService;
use Illuminate\Support\ServiceProvider;

final class ExpoNotificationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config' => config_path(),
        ], 'expo-notifications-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'expo-notifications-migration');

        $this->app->bind(ExpoTokenStorageInterface::class, config('expo-notifications.drivers.token'));
        $this->app->bind(ExpoTicketStorageInterface::class, config('expo-notifications.drivers.ticket'));
        $this->app->bind(ExpoPendingNotificationStorageInterface::class, config('expo-notifications.drivers.notification'));

        $this->app->bind('expo:notifications:send', ExpoDelayedNotificationsSend::class);
        $this->app->bind('expo:purge-tickets', ExpoTicketsPurge::class);

        $this->commands([
            'expo:notifications:send',
            'expo:purge-tickets'
        ]);
    }

    public function register(): void
    {
    }

    public function provides(): array
    {
        return [ExpoNotificationsService::class];
    }
}
