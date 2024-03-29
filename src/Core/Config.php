<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Config\Environment;
use App\Exceptions\ConfigException;

class Config
{
    /**
     * @var array<mixed>
     */
    private array $props;

    /**
     * @throws ConfigException
     */
    public function __construct()
    {
        $this->props = $this->loadProps();
    }

    /**
     * @return mixed[]
     * @throws ConfigException
     */
    public function getArray(string $key): ?array
    {
        $value = $this->props[$key] ?? null;

        if ($value !== null && !is_array($value)) {
            throw new ConfigException('config value for "%s" must be an array');
        }

        return $value;
    }

    /**
     * @throws ConfigException
     */
    public function getInt(string $key, ?int $default = null): ?int
    {
        $value = $this->props[$key] ?? $default;

        if ($value !== null && !is_int($value)) {
            throw new ConfigException(sprintf('config value for "%s" must be an integer', $key));
        }

        return $value;
    }

    /**
     * @throws ConfigException
     */
    public function getString(string $key): ?string
    {
        $value = $this->props[$key] ?? null;

        if ($value !== null && !is_string($value)) {
            throw new ConfigException(sprintf('config value for "%s" must be a string', $key));
        }

        return $value;
    }

    /**
     * @throws ConfigException
     */
    public function requireString(string $key): string
    {
        return $this->getString($key)
            ?? throw new ConfigException(sprintf('config value "%s" not set', $key));
    }

    /**
     * @param mixed[] $props
     * @throws ConfigException
     */
    public static function fromArray(array $props): self
    {
        $instance = new self();
        $instance->props = $props;
        return $instance;
    }

    /**
     * @return array<string,mixed>
     * @throws ConfigException
     */
    private function loadProps(): array
    {
        $fn = __DIR__ . '/../../config/config.php';

        if (!file_exists($fn)) {
            throw new ConfigException('config file not found');
        }

        if (!is_readable($fn)) {
            throw new ConfigException('config file is not readable');
        }

        /**
         * @phpstan-ignore-next-line
         */
        $env = new Environment();

        $data = include $fn;

        if (!is_array($data)) {
            throw new ConfigException('config file does not return an array');
        }

        return $data;
    }
}
