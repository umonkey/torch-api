<?php

declare(strict_types=1);

namespace App\Exceptions;

class UserNotFoundException extends AbstractException
{
    public function __construct()
    {
        parent::__construct('user not found');
    }

    public function getStatusCode(): int
    {
        return 401;
    }
}