# laravel-expo-notifier

Easily send Exponent Notifications with Laravel notifier.

[![Latest Version](https://img.shields.io/github/release/yieldstudio/laravel-expo-notifier?style=flat-square)](https://github.com/yieldstudio/laravel-expo-notifier/releases)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/yieldstudio/laravel-expo-notifier/tests?style=flat-square)](https://github.com/yieldstudio/laravel-expo-notifier/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/yieldstudio/laravel-expo-notifier?style=flat-square)](https://packagist.org/packages/yieldstudio/laravel-expo-notifier)

> L’identifiant de version majeure zéro (0.y.z) est destiné au développement initial. Tout ou partie peut être modifié à tout moment. L’API publique ne devrait pas être considérée comme stable.

## Installation

	composer require yieldstudio/laravel-expo-notifier

## Configure

You can publish the configuration file with:

```shell
php artisan vendor:publish --provider="YieldStudio\LaravelExpoNotifier\ExpoNotificationsServiceProvider" --tag="expo-notifications-config" --tag="expo-notifications-migration"
```

## Usage

### Send notification

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class NewSampleNotification extends Notification
{
    public function __construct()
    {
    }

    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {
        return (new ExpoMessage())
            ->to([$notifiable->expoTokens->value])
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

Clean tickets from outdated tokens
```
php artisan expo:tickets:check
```

You may create schedules to execute these commands.

### Priority management

You can implement the following interface on your notification :
```
YieldStudio\LaravelExpoNotifier\Contracts\UrgentExpoNotificationInterface
```

If method **isUrgent() return false**, your notification will be saved into the database and considered as **Pending**.

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
