<?php

use YieldStudio\LaravelExpoNotifications\TokenStorageMysql;
use YieldStudio\LaravelExpoNotifications\TicketStorageMysql;
use YieldStudio\LaravelExpoNotifications\NotificationMysql;

return [
    'drivers' => [
        'token' => TokenStorageMysql::class,
        'ticket' => TicketStorageMysql::class,
        'notification' => NotificationMysql::class,
    ],
    'database' => [
        'token_table_name' => 'expo_tokens',
        'ticket_table_name' => 'expo_tickets',
        'notification_table_name' => 'expo_notifications',
    ]
];