<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Contracts;

use Illuminate\Support\Collection;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

interface ExpoNotificationsServiceInterface
{
    public function __construct(
        string $apiUrl,
        string $host,
        ExpoPendingNotificationStorageInterface $notificationStorage,
        ExpoTicketStorageInterface $ticketStorage
    );

    public function notify(ExpoMessage|Collection|array $expoMessages): Collection;

    public function receipts(Collection|array $tokenIds): Collection;

    public function getNotificationChunks(): Collection;
}
