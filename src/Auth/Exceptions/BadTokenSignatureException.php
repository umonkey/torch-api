<?php

declare(strict_types=1);

namespace App\Auth\Exceptions;

use App\Exceptions\AbstractException;

class BadTokenSignatureException extends AbstractException
{
    public function __construct()
    {
        parent::__construct('Bad JWT token signature, a corrupt token.');
    }

    public function getStatusCode(): int
    {
        return 401;
    }
}
