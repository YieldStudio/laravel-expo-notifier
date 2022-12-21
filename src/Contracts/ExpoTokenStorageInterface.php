<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoToken;

interface ExpoTokenStorageInterface
{
    public function store(string $token, Model $owner): ExpoToken;

    public function delete(string|array $tokens): void;
}
