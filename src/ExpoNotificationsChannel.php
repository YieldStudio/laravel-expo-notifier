<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifications\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifications\Notifications\Contracts\UrgentExpoNotificationInterface;
use YieldStudio\LaravelExpoNotifications\Services\ExpoNotificationsService;

final class ExpoNotificationsChannel
{

    public function __construct(
        protected readonly ExpoNotificationsService $expoNotificationsService,
        protected readonly ExpoPendingNotificationStorageInterface $expoNotification
    )
    {
    }

    public function send($notifiable, Notification $notification): void
    {
        $expoMessage = $notification->toExpoNotification($notifiable);

        if ($notification instanceof UrgentExpoNotificationInterface && $notification->isUrgent()) {
            $this->expoNotificationsService->notify([$expoMessage]);
        } else {
            $this->expoNotification->store([
                'data' => $expoMessage->toJson()
            ]);
        }
    }
}