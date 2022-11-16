<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services\Dto;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * forked from https://github.com/Alymosul/laravel-exponent-push-notifications/blob/master/src/ExpoMessage.php
 */
final class ExpoMessage implements Jsonable, Arrayable
{
    /**
     * The message to.
     */
    public array $to;

    /**
     * The message title.
     */
    public string $title;

    /**
     * The message body.
     */
    public string $body;

    /**
     * The sound to play when the recipient receives this notification.
     */
    public ?string $sound = 'default';

    /**
     * The number to display next to the push notification (iOS).
     * Specify zero to clear the badge.
     */
    public int $badge = 0;

    /**
     * The number of seconds for which the message may be kept around for redelivery if it has not been delivered yet.
     */
    public int $ttl = 0;

    /**
     * ID of the Notification Channel through which to display this notification on Android devices.
     */
    public string $channelId;

    /**
     * The json data attached to the message.
     */
    public string $jsonData = '{}';

    /**
     * The priority of notification message for Android devices.
     */
    public string $priority = 'default';

    /**
     * Create a message with given body.
     */
    public static function create(string $body = ''): ExpoMessage
    {
        return new ExpoMessage($body);
    }

    /**
     * ExpoMessage constructor.
     */
    public function __construct(string $body = '')
    {
        $this->body = $body;
    }

    /**
     * Set the message to.
     */
    public function to(?array $value): self
    {
        if (is_array($value)) {
            $this->to = $value;
        } else {
            $this->to[] = $value;
        }

        return $this;
    }

    /**
     * Set the message title.
     */
    public function title(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Set the message body.
     */
    public function body(string $value): self
    {
        $this->body = $value;

        return $this;
    }

    /**
     * Enable the message sound.
     */
    public function enableSound(): self
    {
        $this->sound = 'default';

        return $this;
    }

    /**
     * Disable the message sound.
     */
    public function disableSound(): self
    {
        $this->sound = null;

        return $this;
    }

    /**
     * Set the message badge (iOS).
     */
    public function badge(int $value): self
    {
        $this->badge = $value;

        return $this;
    }

    /**
     * Set the time to live of the notification.
     */
    public function ttl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Set the channelId of the notification for Android devices.
     */
    public function channelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * Set the json Data attached to the message.
     *
     * @throws \Exception
     */
    public function jsonData(array|string $data): self
    {
        if (is_array($data)) {
            $data = json_encode($data);
        } elseif (is_string($data)) {
            @json_decode($data);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid json format passed to the setJsonData().');
            }
        }

        $this->jsonData = $data;

        return $this;
    }

    /**
     *  Set the priority of the notification, must be one of [default, normal, high].
     */
    public function priority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get an array representation of the message.
     */
    public function toArray(): array
    {
        $message = [
            'to' => $this->to,
            'title' => $this->title,
            'body' => $this->body,
            'sound' => $this->sound,
            'badge' => $this->badge,
            'ttl' => $this->ttl,
            'data' => $this->jsonData,
            'priority' => $this->priority,
        ];
        if (filled($this->channelId)) {
            $message['channelId'] = $this->channelId;
        }

        return $message;
    }

    public function toExpoData(): array
    {
        return $this->toArray();
    }

    /**
     * Get an json representation of the message.
     */
    public function toJson($options = JSON_THROW_ON_ERROR): bool|string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert json representation to ExpoMessage.
     */
    public function fromJson($jsonData): ExpoMessage
    {
        $data = json_decode($jsonData, true);

        $expoMessage = (new self())
            ->to($data['to'])
            ->title($data['title'])
            ->body($data['body'])
            ->badge($data['badge'])
            ->priority($data['priority'])
            ->ttl($data['ttl'])
            ->jsonData($data['data']);

        if (filled($data['channelId'])) {
            $expoMessage->channelId($data['channelId']);
        }

        if (filled($data['sound'])) {
            $expoMessage->enableSound();
        }

        return $expoMessage;
    }
}
