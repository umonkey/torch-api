<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

abstract class AbstractResponder
{
    /**
     * @param array<mixed> $data
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     */
    protected function sendJSON(ResponseInterface $response, array $data): ResponseInterface
    {
        $payload = json_encode($data, \JSON_THROW_ON_ERROR);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
