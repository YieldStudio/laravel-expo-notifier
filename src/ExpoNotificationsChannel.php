<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Exceptions\ExpoNotificationsException;

final class ExpoNotificationsChannel
{
    public function __construct(
        protected readonly ExpoNotificationsService $expoNotificationsService,
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

        $this->expoNotificationsService->notify($expoMessage);
    }
}
