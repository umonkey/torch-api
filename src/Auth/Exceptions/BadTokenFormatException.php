<?php

declare(strict_types=1);

namespace App\Auth\Exceptions;

use App\Exceptions\UnauthorizedException;

class BadTokenFormatException extends UnauthorizedException
{
    public function __construct()
    {
        parent::__construct('Bad JWT token format');
    }

    public function getStatusCode(): int
    {
        return 401;
    }
}
