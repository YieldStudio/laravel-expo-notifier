<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Support\ServiceProvider;
use YieldStudio\LaravelExpoNotifier\Commands\CheckTickets;
use YieldStudio\LaravelExpoNotifier\Commands\SendPendingNotifications;
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

        $this->app->bind(ExpoNotificationsService::class, function ($app) {
            $apiUrl = config('expo-notifications.service.api_url');
            $host = config('expo-notifications.service.host');

            $instance = new ExpoNotificationsService(
                $apiUrl,
                $host,
                $app->make(ExpoTokenStorageInterface::class),
                $app->make(ExpoTicketStorageInterface::class)
            );

            return $instance;
        });

        $this->commands([
            SendPendingNotifications::class,
            CheckTickets::class,
        ]);
    }

    public function register(): void
    {
    }
}
