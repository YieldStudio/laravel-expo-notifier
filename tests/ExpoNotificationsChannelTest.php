<?php

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFake;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoPendingNotificationStorageMysql;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsService;
use YieldStudio\LaravelExpoNotifier\Tests\User;

it('send notification via ExpoNotificationsChannel should call ExpoNotificationsService notify method', function () {
    DG\BypassFinals::enable();
    NotificationFake::fake();

    $mock = $this->mock(ExpoNotificationsService::class);
    $notificationMock = $this->mock(ExpoPendingNotificationStorageMysql::class);
    $notificationMock->shouldReceive('store')->once();

    $channel = new ExpoNotificationsChannel($mock, $notificationMock);

    $channel->send(new User(), new class extends Notification {
        public function via()
        {
            return [ExpoNotificationsChannel::class];
        }

        public function toExpoNotification(User $notifiable): ExpoMessage
        {
            return (new ExpoMessage())
                ->to(['test'])
                ->title('test title')
                ->body('test body')
                ->channelId('default');
        }
    });
});
