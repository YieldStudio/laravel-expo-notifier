<?php

namespace YieldStudio\LaravelExpoNotifier\Contracts;

use YieldStudio\LaravelExpoNotifier\Services\Dto\ExpoMessage;

interface ExpoPendingNotificationStorageInterface
{
    public function store(ExpoMessage $expoMessage);

    public function retrieve(int $amount = 100);

    public function delete(array $ids);

    public function count();
}
