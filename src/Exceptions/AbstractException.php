<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

abstract class AbstractException extends Exception
{
    /**
     * @return array<mixed>
     */
    public function getResponse(): array
    {
        $parts = explode('\\', get_class($this));
        $className = array_pop($parts);

        return [
            'error' => [
                'exception' => $className,
                'message' => $this->getMessage(),
                'status' => $this->getStatusCode(),
            ],
        ];
    }

    public function getStatusCode(): int
    {
        return 500;
    }
}
