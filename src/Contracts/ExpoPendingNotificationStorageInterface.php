<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoNotification;

interface ExpoPendingNotificationStorageInterface
{
    public function store(ExpoMessage $expoMessage): ExpoNotification;

    /**
     * @return Collection<int, ExpoNotification>
     */
    public function retrieve(int $amount = 100): Collection;

    public function delete(array $ids): void;

    public function count(): int;
}
