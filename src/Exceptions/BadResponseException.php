<?php

declare(strict_types=1);

namespace App\Exceptions;

class BadResponseException extends AbstractException
{
    /**
     * @codeCoverageIgnore
     */
    public function getStatusCode(): int
    {
        return 500;
    }
}
