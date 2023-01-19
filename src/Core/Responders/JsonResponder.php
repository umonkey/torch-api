<?php

declare(strict_types=1);

namespace App\Core\Responders;

use InvalidArgumentException;
use JsonException;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class JsonResponder
{
    /**
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     */
    public function respond(ResponseInterface $response, JsonSerializable $data): ResponseInterface
    {
        $data = json_encode($data, \JSON_THROW_ON_ERROR);

        $response->getBody()->write($data);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
