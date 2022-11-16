<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Notifications\Contracts\UrgentExpoNotificationInterface;
use YieldStudio\LaravelExpoNotifier\Services\ExpoNotificationsService;

final class ExpoNotificationsChannel
{
    public function __construct(
        protected readonly ExpoNotificationsService $expoNotificationsService,
        protected readonly ExpoPendingNotificationStorageInterface $expoNotification
    ) {
    }

    public function send($notifiable, Notification $notification): void
    {
        $expoMessage = $notification->toExpoNotification($notifiable);

        if ($notification instanceof UrgentExpoNotificationInterface && $notification->isUrgent()) {
            $this->expoNotificationsService->notify([$expoMessage]);
        } else {
            $this->expoNotification->store([
                'data' => $expoMessage->toJson(),
            ]);
        }
    }
}
