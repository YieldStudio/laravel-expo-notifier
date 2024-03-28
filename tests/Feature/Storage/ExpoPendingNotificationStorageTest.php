<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->expoMessages = [];

    for ($i = 0; $i < 120; $i++) {
        $this->expoMessages[$i] = ExpoMessage::create()
            ->to([$this->fake()->uuid])
            ->title($this->fake()->sentence)
            ->body($this->fake()->paragraph)
            ->jsonData(['foo' => $this->fake()->slug])
            ->shouldBatch($this->fake()->boolean)
            ->toJson();
    }

    for ($i = 0; $i < 120; $i++) {
        ExpoNotification::create([
            'data' => $this->expoMessages[$i],
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
        ->and($retrievedNotifications->first()->message->toJson())
        ->toBe($this->expoMessages[0])
        ->and($retrievedNotifications->get(1)->message->toJson())
        ->toBe($this->expoMessages[1]);
});

it('retrieves a max of 100 notifications', function () {
    $retrievedNotifications = $this->notificationStorage->retrieve();

    expect($retrievedNotifications)
        ->toBeInstanceOf(Collection::class)
        ->and($retrievedNotifications->last()->id)
        ->toBe(100);
});
