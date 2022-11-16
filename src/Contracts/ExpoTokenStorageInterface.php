<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

interface ExpoTokenStorageInterface
{
    public function getByKey(string $key);
    public function getByValue(string $value);
    public function store(array $data);
    public function delete(array $tokens);
}