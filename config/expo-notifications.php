<?php

use YieldStudio\LaravelExpoNotifier\Storage\ExpoTokenStorageMysql;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoTicketStorageMysql;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoPendingNotificationStorageMysql;

return [
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
        'host' => 'exp.host'
    ]
];