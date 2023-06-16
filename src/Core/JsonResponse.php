<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\BadResponseException;
use InvalidArgumentException;
use JsonException;
use JsonSerializable;
use RuntimeException;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

class JsonResponse extends Response
{
    /**
     * @param string|int|mixed[] $data
     * @throws BadResponseException
     */
    public function __construct(string|int|array|JsonSerializable $data, int $statusCode = 200)
    {
        try {
            $headers = new Headers([], []);
            $headers->setHeader('Content-Type', 'application/json; charset=utf-8');

            $body = (new StreamFactory())->createStream();
            $body->write(json_encode($data, \JSON_THROW_ON_ERROR));

            parent::__construct($statusCode, $headers, $body);
        } catch (InvalidArgumentException | JsonException | RuntimeException) {
            throw new BadResponseException();
        }
    }
}
