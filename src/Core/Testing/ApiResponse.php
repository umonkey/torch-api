<?php

declare(strict_types=1);

namespace App\Core\Testing;

use App\Exceptions\BadResponseException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

/**
 * This is only used in integration tests, so no test coverage.
 *
 * @codeCoverageIgnore
 */
class ApiResponse
{
    public function __construct(
        private readonly string $type,
        private readonly string $body,
        private readonly int $status,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getString(): string
    {
        return $this->body;
    }

    /**
     * @return mixed[]
     * @throws BadResponseException
     */
    public function getJSON(): array
    {
        if ($this->type !== 'application/json') {
            throw new BadResponseException('not a JSON response');
        }

        try {
            return json_decode($this->body, associative: true, flags: \JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new BadResponseException('could not parse JSON response');
        }
    }

    /**
     * @throws BadResponseException
     */
    public static function fromResponse(ResponseInterface $response): self
    {
        $type = self::getContentType($response);

        return new self(
            type: $type,
            body: (string)$response->getBody(),
            status: $response->getStatusCode(),
        );
    }

    /**
     * @throws BadResponseException
     */
    private static function getContentType(ResponseInterface $response): string
    {
        $header = $response->getHeaderLine('content-type');

        if ($header === '') {
            throw new BadResponseException('content-type header not set');
        }

        $parts = explode(';', $header);
        return $parts[0];
    }
}
