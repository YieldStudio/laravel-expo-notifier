<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Dto;

use Illuminate\Contracts\Support\Arrayable;

final class PushReceiptResponse implements Arrayable
{
    public string $status;

    public ?string $id = null;

    public ?string $message = null;

    public ?array $details = null;

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function details(array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'status' => $this->status,
        ];

        if (filled($this->message)) {
            $data['message'] = $this->message;
        }

        if (filled($this->details)) {
            $data['details'] = $this->details;
        }

        return $data;
    }
}
