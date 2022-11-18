<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Dto;

use Illuminate\Contracts\Support\Arrayable;

final class ExpoNotification implements Arrayable
{
    public int $id;

    public ExpoMessage $message;

    public static function make(int $id, ExpoMessage $message): ExpoNotification
    {
        return (new ExpoNotification())
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
