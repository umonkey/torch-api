<?php

declare(strict_types=1);

namespace App\Database\Entities;

use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\PropertyNotSetException;
use App\Database\Exceptions\WrongValueTypeException;

abstract class AbstractEntity
{
    protected const PROP_TYPES = [];

    /**
     * @var array<string,mixed>
     */
    protected array $props;

    /**
     * @param array<string,mixed> $props
     */
    public function __construct(array $props = [])
    {
        $this->props = array_replace(static::getDefaults(), $props);
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return $this->props;
    }

    /**
     * @return array<mixed>
     */
    public function serialize(): array
    {
        return $this->props;
    }

    public function validate(): void
    {
    }

    /**
     * @throws DatabaseException
     */
    protected function getInt(string $key): ?int
    {
        $value = $this->props[$key] ?? null;

        if ($value !== null && !is_int($value)) {
            throw new WrongValueTypeException('property value is not an int');
        }

        return $value;
    }

    /**
     * @throws DatabaseException
     */
    protected function getString(string $key): ?string
    {
        $value = $this->props[$key] ?? null;

        if ($value !== null && !is_string($value)) {
            throw new WrongValueTypeException(sprintf('property "%s" value not set', $key));
        }

        return $value;
    }

    /**
     * @throws DatabaseException
     */
    protected function requireInt(string $key): int
    {
        return $this->getInt($key)
            ?? throw new PropertyNotSetException(sprintf('property "%s" value not set', $key));
    }

    /**
     * @throws DatabaseException
     */
    protected function requireString(string $key): string
    {
        return $this->getString($key)
            ?? throw new PropertyNotSetException(sprintf('property "%s" value not set', $key));
    }

    protected function setInt(string $key, int $value): void
    {
        $this->props[$key] = $value;
    }

    protected function setString(string $key, string $value): void
    {
        $this->props[$key] = $value;
    }

    /**
     * @return mixed[]
     **/
    abstract protected static function getDefaults(): array;
}
