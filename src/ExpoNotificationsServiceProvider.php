<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Support\ServiceProvider;
use YieldStudio\LaravelExpoNotifier\Commands\ExpoDelayedNotificationsSend;
use YieldStudio\LaravelExpoNotifier\Commands\ExpoTicketsPurge;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Services\ExpoNotificationsService;

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
            'expo:purge-tickets',
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
