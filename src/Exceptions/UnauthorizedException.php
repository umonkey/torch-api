<?php

declare(strict_types=1);

namespace App\Exceptions;

class UnauthorizedException extends AbstractException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getStatusCode(): int
    {
        return 401;
    }
}
