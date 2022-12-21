<?php

declare(strict_types=1);

use YieldStudio\LaravelExpoNotifier\Storage\ExpoPendingNotificationStorageMysql;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoTicketStorageMysql;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoTokenStorageMysql;

return [
    /*
     * If set to true, when InvalidExpoToken event is triggered, the token is automatically deleted.
     */
    'automatically_delete_token' => true,

    'drivers' => [
        'token' => ExpoTokenStorageMysql::class,
        'ticket' => ExpoTicketStorageMysql::class,
        'notification' => ExpoPendingNotificationStorageMysql::class,
    ],
    'database' => [
        'tokens_table_name' => 'expo_tokens',
        'tickets_table_name' => 'expo_tickets',
        'notifications_table_name' => 'expo_notifications',
    ],
    'service' => [
        'api_url' => 'https://exp.host/--/api/v2/push',
        'host' => 'exp.host',
    ],
];
