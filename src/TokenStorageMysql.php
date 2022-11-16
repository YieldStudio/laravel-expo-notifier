<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifications;

use YieldStudio\LaravelExpoNotifications\Contracts\TokenStorageInterface;
use YieldStudio\LaravelExpoNotifications\Models\ExpoToken;

final class TokenStorageMysql implements TokenStorageInterface
{
    public function getByKey(string $key): ?ExpoToken
    {
        return ExpoToken::where('key', '=', $key)
            ->first();
    }

    public function getByValue(string $value): ?ExpoToken
    {
        return ExpoToken::where('value', '=', $value)
            ->first();
    }

    public function store(array $data): ExpoToken
    {
        return ExpoToken::create($data);
    }

    public function delete(array $tokens): bool
    {
        return (bool)ExpoToken::whereIn('value', $tokens)->delete();
    }
}