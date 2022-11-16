<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoNotification as ExpoNotificationDto;

final class ExpoPendingNotificationStorageMysql implements ExpoPendingNotificationStorageInterface
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
