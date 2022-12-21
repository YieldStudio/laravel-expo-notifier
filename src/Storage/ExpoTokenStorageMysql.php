<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTokenStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoToken as ExpoTokenDto;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

class ExpoTokenStorageMysql implements ExpoTokenStorageInterface
{
    public function store(string $token, Model $owner): ExpoTokenDto
    {
        $token = ExpoToken::query()->updateOrCreate([
            'value' => $token,
            'owner_type' => $owner->getMorphClass(),
            'owner_id' => $owner->getKey(),
        ]);

        return ExpoTokenDto::make($token->id, $token->value, $owner);
    }

    public function delete(string|array $tokens): void
    {
        $tokens = Arr::wrap($tokens);
        ExpoToken::query()->whereIn('value', $tokens)->delete();
    }
}
