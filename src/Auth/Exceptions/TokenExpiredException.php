<?php

declare(strict_types=1);

namespace App\Auth\Exceptions;

use App\Exceptions\UnauthorizedException;

class TokenExpiredException extends UnauthorizedException
{
    public function __construct()
    {
        parent::__construct('Token expired, please request a new one.');
    }

    public function getStatusCode(): int
    {
        return 401;
    }
}
