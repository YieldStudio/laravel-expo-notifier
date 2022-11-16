<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifications\Contracts\NotificationInterface;
use YieldStudio\LaravelExpoNotifications\Models\ExpoNotification;
use YieldStudio\LaravelExpoNotifications\Services\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifications\Services\Dto\ExpoNotification as ExpoNotificationDto;

final class NotificationMysql implements NotificationInterface
{
    public function store(array $data): ExpoNotification
    {
        return ExpoNotification::create($data);
    }

    public function retrieve(int $amount = 100): Collection
    {
        return ExpoNotification::take($amount)
            ->get()
            ->map(function ($notification) {
                return (new ExpoNotificationDto())
                    ->id($notification->id)
                    ->expoMessage(
                        (new ExpoMessage())
                            ->fromJson($notification->data)
                    );
            });
    }

    public function delete(array $ids): bool
    {
        ExpoNotification::whereIn('id', $ids)->delete();

        return true;
    }

    public function total(): int
    {
        return ExpoNotification::count();
    }
}