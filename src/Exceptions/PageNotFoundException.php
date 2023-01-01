<?php

declare(strict_types=1);

namespace App\Exceptions;

class PageNotFoundException extends AbstractException
{
    public function getStatusCode(): int
    {
        return 404;
    }
}