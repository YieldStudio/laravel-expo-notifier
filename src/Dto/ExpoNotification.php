<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Dto;

use Illuminate\Contracts\Support\Arrayable;

final class ExpoNotification implements Arrayable
{
    public int|string $id;

    public ExpoMessage $message;

    public bool $sent;

    public static function make(int|string $id, ExpoMessage $message, bool $sent): ExpoNotification
    {
        return (new ExpoNotification)
            ->id($id)
            ->message($message)
            ->sent($sent);
    }

    public function id(int|string $value): self
    {
        $this->id = $value;

        return $this;
    }

    public function message(ExpoMessage $value): self
    {
        $this->message = $value;

        return $this;
    }

    public function sent(bool $value): self
    {
        $this->sent = $value;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'expo_message' => $this->message,
            'sent' => $this->sent,
        ];
    }
}
