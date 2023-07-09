<?php

declare(strict_types=1);

namespace App\Auth\Exceptions;

use App\Exceptions\UnauthorizedException;

class BadTokenSignatureException extends UnauthorizedException
{
    public function __construct()
    {
        parent::__construct('Bad JWT token signature, a corrupt token.');
    }

    /**
     * @codeCoverageIgnore
     */
    public function getStatusCode(): int
    {
        return 401;
    }
}
