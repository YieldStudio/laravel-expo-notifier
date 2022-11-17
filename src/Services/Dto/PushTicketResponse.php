<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services\Dto;

final class PushTicketResponse
{
    public string $status;
    public ?string $ticketId = null;
    public ?string $message = null;
    public ?array $details = null;

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function ticketId(string $ticketId): self
    {
        $this->ticketId = $ticketId;

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
            'status' => $this->status,
        ];

        if (filled($this->ticketId)) {
            $data['ticket_id'] = $this->ticketId;
        }

        if (filled($this->message)) {
            $data['message'] = $this->message;
        }

        if (filled($this->details)) {
            $data['details'] = $this->details;
        }

        return $data;
    }
}
