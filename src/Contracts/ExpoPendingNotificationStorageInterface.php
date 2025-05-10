<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoNotification;

interface ExpoPendingNotificationStorageInterface
{
    public function store(ExpoMessage $expoMessage, bool $sent = false): ExpoNotification;

    /**
     * @return Collection<int, ExpoNotification>
     */
    public function retrieve(int $amount = 100, bool $sent = false): Collection;

    public function delete(array $ids): void;

    public function updateSent(array $ids, bool $sent = true): void;

    public function count(): int;
}
