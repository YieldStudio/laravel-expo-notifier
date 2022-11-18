<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Storage;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoNotification as ExpoNotificationDto;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;

class ExpoPendingNotificationStorageMysql implements ExpoPendingNotificationStorageInterface
{
    public function store(ExpoMessage $expoMessage): ExpoNotificationDto
    {
        $notification = ExpoNotification::create([
            'data' => $expoMessage->toJson(),
        ]);

        return ExpoNotificationDto::make($notification->id, $expoMessage);
    }

    public function retrieve(int $amount = 100): Collection
    {
        return ExpoNotification::query()
            ->take($amount)
            ->get()
            ->map(function ($notification) {
                return ExpoNotificationDto::make($notification->id, ExpoMessage::fromJson($notification->data));
            });
    }

    public function delete(array $ids): void
    {
        ExpoNotification::query()->whereIn('id', $ids)->delete();
    }

    public function count(): int
    {
        return ExpoNotification::query()->count();
    }
}
