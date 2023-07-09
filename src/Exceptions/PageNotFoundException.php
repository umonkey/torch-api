<?php

declare(strict_types=1);

namespace App\Exceptions;

class PageNotFoundException extends AbstractException
{
    /**
     * @codeCoverageIgnore
     */
    public function getStatusCode(): int
    {
        return 404;
    }
}
