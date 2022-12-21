<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Dto;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

final class ExpoToken implements Arrayable
{
    public int|string $id;
    public string $value;
    public Model $owner;

    public static function make(int|string $id, string $value, Model $owner): ExpoToken
    {
        return (new ExpoToken())
            ->id($id)
            ->value($value)
            ->owner($owner);
    }

    public function id(int|string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function owner(Model $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'owner_type' => get_class($this->owner),
            'owner_id' => $this->owner->getKey(),
        ];
    }
}
