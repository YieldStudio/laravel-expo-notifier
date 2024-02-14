<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;

uses(RefreshDatabase::class);

beforeEach(function () {
    for ($i = 0; $i < 120; $i++) {
        ExpoNotification::create([
            'data' => json_encode([
                'foo' => fake()->slug,
            ], JSON_THROW_ON_ERROR),
        ]);
    }

    $this->notifications = ExpoNotification::all();
    $this->notificationStorage = app(config('expo-notifications.drivers.notification'));
});

it('retrieves notifications from storage', function () {
    $retrievedNotifications = $this->notificationStorage->retrieve();

    expect($retrievedNotifications)
        ->toBeInstanceOf(Collection::class)
        ->and($retrievedNotifications->first()->id)
        ->toBe($this->notifications->first()->id)
        ->and($retrievedNotifications->first()->message->foo)
        ->toBe(json_decode($this->notifications->first()->data, true, 512, JSON_THROW_ON_ERROR)['foo'])
        ->and($retrievedNotifications->get(2)->id)
        ->toBe($this->notifications->get(2)->id)
        ->and($retrievedNotifications->get(2)->message->foo)
        ->toBe(json_decode($this->notifications->get(2)->data, true, 512, JSON_THROW_ON_ERROR)['foo']);
});

it('retrieves a max of 100 notifications', function () {
    $retrievedNotifications = $this->notificationStorage->retrieve();

    expect($retrievedNotifications)
        ->toBeInstanceOf(Collection::class)
        ->and($retrievedNotifications->last()->id)
        ->toBe(100);
});
