# laravel-expo-notifier

Easily manage Expo notifications with Laravel. Support batched notifications.

[![Latest Version](https://img.shields.io/github/release/yieldstudio/laravel-expo-notifier?style=flat-square)](https://github.com/yieldstudio/laravel-expo-notifier/releases)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/yieldstudio/laravel-expo-notifier/tests.yml?branch=main&style=flat-square)](https://github.com/yieldstudio/laravel-expo-notifier/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/yieldstudio/laravel-expo-notifier?style=flat-square)](https://packagist.org/packages/yieldstudio/laravel-expo-notifier)

> Major version zero (0.y.z) is for initial development. Anything MAY change at any time. The public API SHOULD NOT be considered stable.

## Installation

	composer require yieldstudio/laravel-expo-notifier

## Configure

You must publish the configuration file with:

```shell
php artisan vendor:publish --provider="YieldStudio\LaravelExpoNotifier\ExpoNotificationsServiceProvider" --tag="expo-notifications-config" --tag="expo-notifications-migration"
```

### Available environment variables
- `EXPO_PUSH_NOTIFICATIONS_PER_REQUEST_LIMIT` : sets the max notifications sent on a bulk request. [The official documentation says the limit should be 100](https://docs.expo.dev/push-notifications/sending-notifications/#request-errors) but in fact it's failing. You can tweak it by setting a value under 100.
- `EXPO_NOTIFICATIONS_ACCESS_TOKEN` : sets the Expo access token used to send notifications with an [additional layer of security](https://docs.expo.dev/push-notifications/sending-notifications/#additional-security). You can get your access token from [your Expo dashboard](https://expo.dev/accounts/[account]/settings/access-tokens).

## Usage

### Add relation for notifiable class

In order to use Notification, you need to declare the relation `expoTokens` on your Notifiable class.
Two Trait are available to help you :
- `HasUniqueExpoToken` if your notifiable class can have only one expo token
- `HasManyExpoToken` if your notifiable class can have many expo token

### Send notification

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class NewSampleNotification extends Notification
{
    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {
        return (new ExpoMessage())
            // ->to($notifiable->expoTokens->pluck('value')->toArray()) if using HasManyExpoToken
            ->to([$notifiable->expoTokens->value])
            ->notifiable($notifiable) // allow possibility to store the receiver of the notification
            ->title('A beautiful title')
            ->body('This is a content')
            ->channelId('default');
    }
}
```

### Commands usage

Send database pending notifications
```
php artisan expo:notifications:send
```

Clean sent notification from database
```
php artisan expo:notifications:clear
```

Clean tickets from outdated tokens
```
php artisan expo:tickets:check
```

You may create schedules to execute these commands.

### Batch support

You can send notification in the next batch : 
```php
(new ExpoMessage())
    ->to([$notifiable->expoTokens->value])
    ->title('A beautiful title')
    ->body('This is a content')
    ->channelId('default')
    ->shouldBatch();
```

Don't forget to schedule the `expo:notifications:send` command.

## Unit tests

To run the tests, just run `composer install` and `composer test`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://raw.githubusercontent.com/YieldStudio/.github/main/CONTRIBUTING.md) for details.

### Security

If you've found a bug regarding security please mail [contact@yieldstudio.fr](mailto:contact@yieldstudio.fr) instead of using the issue tracker.

## Credits

- [David Tang](https://github.com/dtangdev)
- [James Hemery](https://github.com/jameshemery)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
