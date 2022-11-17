<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services\Dto;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * @see https://github.com/Alymosul/laravel-exponent-push-notifications/blob/master/src/ExpoMessage.php
 * @author Alymosul<aly.suleiman@kfzteile24.de>
 * @author David Tang<dtang.dev@gmail.com>
 */
final class ExpoMessage implements Jsonable, Arrayable
{
    public array $to;

    public string $title;

    public string $body;

    public ?string $sound = 'default';

    public int $badge = 0;

    public int $ttl = 0;

    public string $channelId;

    public string $jsonData = '{}';

    public string $priority = 'default';

    public static function create(string $body = ''): ExpoMessage
    {
        return new ExpoMessage($body);
    }

    public function __construct(string $body = '')
    {
        $this->body = $body;
    }

    public function to(?array $value): self
    {
        if (is_array($value)) {
            $this->to = $value;
        } else {
            $this->to[] = $value;
        }

        return $this;
    }

    public function title(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function body(string $value): self
    {
        $this->body = $value;

        return $this;
    }

    public function enableSound(): self
    {
        $this->sound = 'default';

        return $this;
    }

    public function disableSound(): self
    {
        $this->sound = null;

        return $this;
    }

    public function badge(int $value): self
    {
        $this->badge = $value;

        return $this;
    }

    public function ttl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function channelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

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

    public function priority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

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

    public function toJson($options = JSON_THROW_ON_ERROR): bool|string
    {
        return json_encode($this->toArray(), $options);
    }

    public static function fromJson($jsonData): ExpoMessage
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
