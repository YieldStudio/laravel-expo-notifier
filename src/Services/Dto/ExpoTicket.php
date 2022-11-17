<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Services\Dto;

final class ExpoTicket
{
    public string $id;
    public string $token;

    public static function make(string $id, string $token): ExpoTicket
    {
        return (new ExpoTicket)
            ->id($id)
            ->token($token);
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
        ];
    }
}
