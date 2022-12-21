<?php

declare(strict_types=1);

namespace YieldStudio\LaravelExpoNotifier\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

final class ExpoNotificationsException extends Exception
{
    public function __construct(private readonly ResponseInterface $response)
    {
        parent::__construct(sprintf(
            'Expo service error: HTTP `%s` response: %s',
            $this->response->getStatusCode(),
            $this->response->getBody()
        ));
    }
}
