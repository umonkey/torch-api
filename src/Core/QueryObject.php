<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\BadRequestException;
use Psr\Http\Message\ServerRequestInterface;

class QueryObject
{
    /**
     * @param array<mixed> $params
     */
    public function __construct(private array $params)
    {
    }

    /**
     * @throws BadRequestException
     */
    public function getBool(string $key): bool
    {
        if (!array_key_exists($key, $this->params)) {
            return false;
        }

        if ($this->params[$key] === 'yes') {
            return true;
        }

        throw new BadRequestException(sprintf('query parameter "%s" must be missing or contain "yes"', $key));
    }

    /**
     * @param non-empty-list<string> $values
     * @throws BadRequestException
     */
    public function getEnum(string $key, array $values): string
    {
        $value = $this->getString($key);

        if ($value === null) {
            return $values[0];
        }

        if (!in_array($value, $values, true)) {
            throw new BadRequestException(sprintf('wrong option for query param "%s"', $key));
        }

        return $value;
    }

    /**
     * @throws BadRequestException
     */
    public function getInt(string $key): ?int
    {
        $value = $this->params[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (!is_string($value) || !ctype_digit($value)) {
            throw new BadRequestException(sprintf('query parameter "%s" must be a number', $key));
        }

        return (int)$value;
    }

    /**
     * @throws BadRequestException
     */
    public function getString(string $key): ?string
    {
        $value = $this->params[$key] ?? null;

        if ($value !== null && !is_string($value)) {
            throw new BadRequestException(sprintf('query parameter "%s" must be a string', $key));
        }

        return $value;
    }

    /**
     * @throws BadRequestException
     */
    public function requireString(string $key): string
    {
        return $this->getString($key)
            ?? throw new BadRequestException(sprintf('query parameter "%s" not set', $key));
    }

    public static function fromRequest(ServerRequestInterface $req): self
    {
        return new self($req->getQueryParams());
    }
}
