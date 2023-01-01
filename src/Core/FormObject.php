<?php

/**
 * Use this in actions to extract data from request payloads, with some
 * validation added, instead of using array access.  Example:
 *
 * $form = FormObject::fromJson($request);
 * $userName = $form->requireString("user");
 */

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\BadRequestException;
use JsonException;
use Psr\Http\Message\ServerRequestInterface;

class FormObject
{
    /**
     * @param array<mixed> $params
     */
    public function __construct(private array $params)
    {
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->params;
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

        throw new BadRequestException(sprintf('request parameter "%s" must be missing or contain "yes"', $key));
    }

    /**
     * @throws BadRequestException
     */
    public function getInt(string $key): ?int
    {
        $value = $this->params[$key] ?? null;

        if ($value !== null && !is_int($value)) {
            throw new BadRequestException(sprintf('request parameter "%s" must be an integer', $key));
        }

        return $value;
    }

    /**
     * @throws BadRequestException
     */
    public function getString(string $key): ?string
    {
        $value = $this->params[$key] ?? null;

        if ($value !== null && !is_string($value)) {
            throw new BadRequestException(sprintf('request parameter "%s" must be a string', $key));
        }

        return $value;
    }

    /**
     * @return string[]
     * @throws BadRequestException
     */
    public function getStrings(string $key): array
    {
        $values = $this->params[$key] ?? [];

        foreach ($values as $value) {
            if (!is_string($value)) {
                throw new BadRequestException(sprintf('request parameter "%s" must be a list of strings', $key));
            }
        }

        return $values;
    }

    /**
     * @throws BadRequestException
     */
    public function requireString(string $key): string
    {
        return $this->getString($key)
            ?? throw new BadRequestException(sprintf('request parameter "%s" is missing', $key));
    }

    /**
     * @throws BadRequestException
     */
    public static function fromJSON(ServerRequestInterface $request): self
    {
        $ct = $request->getHeaderLine('Content-Type');

        if ($ct !== 'application/json') {
            throw new BadRequestException('request content type must be application/json');
        }

        $body = (string)$request->getBody();

        try {
            $data = json_decode($body, true, 512, \JSON_THROW_ON_ERROR) ?? [];
        } catch (JsonException) {
            throw new BadRequestException('error parsing json');
        }

        return new self($data);
    }

    /**
     * @throws BadRequestException
     */
    public static function fromRequest(ServerRequestInterface $request): self
    {
        $ct = $request->getHeaderLine('Content-Type');

        $ct = explode(';', $ct);

        $ct = trim($ct[0] ?? '');

        if ($ct !== 'application/x-www-form-urlencoded') {
            throw new BadRequestException('request content type must be application/x-www-form-urlencoded');
        }

        $data = (array)$request->getParsedBody();

        return new self($data);
    }
}
