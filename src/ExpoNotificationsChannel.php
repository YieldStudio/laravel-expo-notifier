<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoPendingNotificationStorageInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\UrgentExpoNotificationInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;

final class ExpoNotificationsChannel
{
    public function __construct(
        protected readonly ExpoNotificationsService $expoNotificationsService,
        protected readonly ExpoPendingNotificationStorageInterface $expoNotification
    ) {
    }

    /**
     * @throws ExpoNotificationsException
     */
    public function send($notifiable, Notification $notification): void
    {
        /** @var ExpoMessage $expoMessage */
        $expoMessage = $notification->toExpoNotification($notifiable);

        if (empty($expoMessage->to)) {
            return;
        }

        if ($notification instanceof UrgentExpoNotificationInterface && $notification->isUrgent()) {
            $response = $this->expoNotificationsService->notify($expoMessage);

            $this->expoNotificationsService->storeTicketsFromResponse(
                collect($expoMessage->to),
                $response
            );
        } else {
            $this->expoNotification->store($expoMessage);
        }
    }
}
