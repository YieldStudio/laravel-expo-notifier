<?php

namespace YieldStudio\LaravelExpoNotifications\Contracts;

interface ExpoPendingNotificationStorageInterface
{
    public function store(array $data);

    public function retrieve(int $amount = 100);

    public function delete(array $ids);

    public function total();
}