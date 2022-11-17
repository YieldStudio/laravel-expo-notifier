<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoNotification as ExpoNotificationDto;

class ExpoPendingNotificationStorageMysql implements ExpoPendingNotificationStorageInterface
{
    public function store(ExpoMessage $expoMessage): ExpoNotificationDto
    {
        $notification = ExpoNotification::create([
            'data' => $expoMessage->toJson(),
        ]);

        return ExpoNotificationDto::make($notification->id, $expoMessage);
    }

    /**
     * @param int $amount
     * @return Collection<int, ExpoNotificationDto>
     */
    public function retrieve(int $amount = 100): Collection
    {
        return ExpoNotification::take($amount)
            ->get()
            ->map(function ($notification) {
                return (new ExpoNotificationDto())
                    ->id($notification->id)
                    ->message(ExpoMessage::fromJson($notification->data));
            });
    }

    public function delete(array $ids): void
    {
        ExpoNotification::whereIn('id', $ids)->delete();
    }

    public function count(): int
    {
        return ExpoNotification::count();
    }
}
