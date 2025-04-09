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
        // https://docs.expo.dev/push-notifications/sending-notifications/#additional-security
        'access_token' => env('EXPO_NOTIFICATIONS_ACCESS_TOKEN', null),
        'limits' => [
            // https://docs.expo.dev/push-notifications/sending-notifications/#request-errors
            'push_notifications_per_request' => (int) env('EXPO_PUSH_NOTIFICATIONS_PER_REQUEST_LIMIT', 99),
        ],
        // https://expo.dev/blog/expo-adds-support-for-fcm-http-v1-api
        'use_fcm_legacy_api' => (bool) env('EXPO_USE_FCM_LEGACY_API', false),
    ],
];
