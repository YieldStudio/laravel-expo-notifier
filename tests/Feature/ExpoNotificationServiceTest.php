<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoNotificationsServiceInterface;
use YieldStudio\LaravelExpoNotifier\Contracts\ExpoTicketStorageInterface;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->messages = collect();

    for ($i = 0; $i < 120; $i++) {
        $this->messages->push(
            (new ExpoMessage)
                ->to([Str::orderedUuid()->toString()])
                ->title("A beautiful title #$i")
                ->body('This is a content')
                ->channelId('default')
        );
    }
    $this->notificationService = app(ExpoNotificationsServiceInterface::class);
});

it("creates 2 chunks if we're sending 20 notifications above limit", function () {
    $this->notificationService->notify($this->messages);
    $count = $this->notificationService->getNotificationChunks()->count();

    expect($count)->toBe(2)
        ->and(app(ExpoTicketStorageInterface::class)->count())
        ->toBe($this->messages->count());
});
