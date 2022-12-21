<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Dto;

use Illuminate\Contracts\Support\Arrayable;

final class ExpoToken implements Arrayable
{
    public string $id;

    public string $value;

    public static function make(string $id, string $value): ExpoToken
    {
        return (new ExpoToken())
            ->id($id)
            ->value($value);
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
        ];
    }
}
