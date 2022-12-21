<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Dto;

use Illuminate\Contracts\Support\Arrayable;

final class ExpoTicket implements Arrayable
{
    public int|string $id;

    public string $token;

    public static function make(int|string $id, string $token): ExpoTicket
    {
        return (new ExpoTicket())
            ->id($id)
            ->token($token);
    }

    public function id(int|string $id): self
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
