<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services\Dto;

final class ExpoNotification
{
    /**
     * The notification id.
     */
    public int $id;

    /**
     * The notification expoMessage
     */
    public ExpoMessage $message;

    /**
     * Set the notification id
     */
    public function id(int $value): self
    {
        $this->id = $value;

        return $this;
    }

    /**
     * Set the expoMessage
     */
    public function expoMessage(ExpoMessage $value): self
    {
        $this->message = $value;

        return $this;
    }


    /**
     * Get an array representation of the notification.
     */
    public function toArray(): array
    {
        return [
            'id'        =>  $this->id,
            'expo_message'     =>  $this->message
        ];
    }

}