<?php

use YieldStudio\LaravelExpoNotifications\ExpoTokenStorageMysql;
use YieldStudio\LaravelExpoNotifications\ExpoTicketStorageMysql;
use YieldStudio\LaravelExpoNotifications\ExpoPendingNotificationStorageMysql;

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
    ]
];