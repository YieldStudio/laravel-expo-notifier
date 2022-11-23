<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Storage;

use Illuminate\Support\Arr;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoToken as ExpoTokenDto;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

class ExpoTokenStorageMysql implements ExpoTokenStorageInterface
{
    public function store(array $data): ExpoTokenDto
    {
        $token = ExpoToken::create($data);

        return ExpoTokenDto::make($token->id, $token->value);
    }

    public function delete(string|array $tokens): void
    {
        $tokens = Arr::wrap($tokens);
        ExpoToken::query()->whereIn('value', $tokens)->delete();
    }
}
