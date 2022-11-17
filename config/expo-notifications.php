<?php

use YieldStudio\LaravelExpoNotifier\ExpoTokenStorageMysql;
use YieldStudio\LaravelExpoNotifier\ExpoTicketStorageMysql;
use YieldStudio\LaravelExpoNotifier\ExpoPendingNotificationStorageMysql;

return [
    'drivers' => [
        'token' => ExpoTokenStorageMysql::class,
        'ticket' => ExpoTicketStorageMysql::class,
        'notification' => ExpoPendingNotificationStorageMysql::class,
    ],
    'database' => [
        'token_table_name' => 'expo_tokens',
        'ticket_table_name' => 'expo_tickets',
        'notification_table_name' => 'expo_notifications',
    ],
    'service' => [
        'api_url' => 'https://exp.host/--/api/v2/push',
        'host' => 'exp.host'
    ]
];