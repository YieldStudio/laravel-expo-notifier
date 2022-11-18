<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

use YieldStudio\LaravelExpoNotifier\Dto\ExpoToken;

interface ExpoTokenStorageInterface
{
    public function store(array $data): ExpoToken;

    public function delete(array $tokens): void;
}
