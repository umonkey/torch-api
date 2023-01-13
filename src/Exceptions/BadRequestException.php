<?php

declare(strict_types=1);

namespace App\Exceptions;

class BadRequestException extends AbstractException
{
    public function getStatusCode(): int
    {
        return 400;
    }
}
