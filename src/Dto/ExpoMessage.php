<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Dto;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * @see https://github.com/Alymosul/laravel-exponent-push-notifications/blob/master/src/ExpoMessage.php
 *
 * @author Alymosul<aly.suleiman@kfzteile24.de>
 * @author David Tang<david@yieldstudio.fr>
 * @author James Hemery<james@yieldstudio.fr>
 */
final class ExpoMessage implements Arrayable, Jsonable
{
    public array $to;

    public ?string $title = null;

    /** iOS only */
    public ?string $subtitle = null;

    public ?string $body = null;

    /** iOS only */
    public ?string $sound = null;

    /** iOS only */
    public ?int $badge = null;

    public ?int $ttl = null;

    /** Android only */
    public ?string $channelId = null;

    public ?string $jsonData = null;

    public string $priority = 'default';

    /** iOS only */
    public bool $mutableContent = false;

    public bool $shouldBatch = false;

    public static function create(): ExpoMessage
    {
        return new ExpoMessage();
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

    public function title(?string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function subtitle(?string $value): self
    {
        $this->subtitle = $value;

        return $this;
    }

    public function body(?string $value): self
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

    public function badge(?int $value): self
    {
        $this->badge = $value;

        return $this;
    }

    public function ttl(?int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function channelId(?string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    public function jsonData(array|string|null $data): self
    {
        if (is_string($data)) {
            // Check JSON validity
            json_decode($data, null, 512, JSON_THROW_ON_ERROR);
        } elseif (! is_null($data)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $this->jsonData = $data;

        return $this;
    }

    public function priority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function mutableContent(bool $mutableContent = true): self
    {
        $this->mutableContent = $mutableContent;

        return $this;
    }

    public function shouldBatch(bool $shouldBatch = true): self
    {
        $this->shouldBatch = $shouldBatch;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'to' => $this->to,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'body' => $this->body,
            'sound' => $this->sound,
            'badge' => $this->badge,
            'ttl' => $this->ttl,
            'data' => $this->jsonData,
            'priority' => $this->priority,
            'channelId' => $this->channelId,
            'mutableContent' => $this->mutableContent,
        ];
    }

    public function toExpoData(): array
    {
        return array_filter($this->toArray(), fn ($item) => ! is_null($item));
    }

    public function toJson($options = JSON_THROW_ON_ERROR): bool|string
    {
        return json_encode($this->toArray(), $options);
    }

    public static function fromJson($jsonData): ExpoMessage
    {
        $data = json_decode($jsonData, true);

        $expoMessage = new self();
        foreach ($data as $key => $value) {
            if ($key === 'data') {
                $expoMessage->jsonData($value);
            }
            $expoMessage->{$key} = $value;
        }

        return $expoMessage;
    }
}
