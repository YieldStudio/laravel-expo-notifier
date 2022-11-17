<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services\Dto;

use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoNotification as ExpoNotificationDto;

final class ExpoNotification
{
    public int $id;

    public ExpoMessage $message;

    public static function make(int $id, ExpoMessage $message): ExpoNotificationDto
    {
        return (new ExpoNotificationDto())
            ->id($id)
            ->message($message);
    }

    public function id(int $value): self
    {
        $this->id = $value;

        return $this;
    }

    public function message(ExpoMessage $value): self
    {
        $this->message = $value;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'expo_message' => $this->message,
        ];
    }
}
