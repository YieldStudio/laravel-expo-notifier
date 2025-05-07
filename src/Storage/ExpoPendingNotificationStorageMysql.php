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
    public function store(ExpoMessage $expoMessage, bool $sent = false): ExpoNotificationDto
    {
        $notification = ExpoNotification::create([
            'data' => $expoMessage->toJson(),
            'sent' => $sent,
        ]);

        return ExpoNotificationDto::make($notification->id, $expoMessage, $sent);
    }

    public function retrieve(int $amount = 100, bool $sent = false): Collection
    {
        return ExpoNotification::query()
            ->where('sent', $sent)
            ->take($amount)
            ->get()
            ->map(function ($notification) {
                return ExpoNotificationDto::make($notification->id, ExpoMessage::fromJson($notification->data), (bool) $notification->sent);
            });
    }

    public function delete(array $ids): void
    {
        ExpoNotification::query()->whereIn('id', $ids)->delete();
    }

    public function updateSent(array $ids, bool $sent = true): void
    {
        ExpoNotification::query()->whereIn('id', $ids)->update(['sent' => $sent]);
    }

    public function count(): int
    {
        return ExpoNotification::query()->where('sent', false)->count();
    }
}
