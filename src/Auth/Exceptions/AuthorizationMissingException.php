<?php

declare(strict_types=1);

namespace App\Auth\Exceptions;

use App\Exceptions\UnauthorizedException;

class AuthorizationMissingException extends UnauthorizedException
{
    public function __construct()
    {
        parent::__construct('No authorization header in the request.');
    }

    public function getStatusCode(): int
    {
        return 401;
    }
}
