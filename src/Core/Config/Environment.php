<?php

declare(strict_types=1);

namespace App\Core\Config;

use App\Exceptions\ConfigException;

class Environment
{
    /**
     * @var array<string,string>
     */
    private array $props = [];

    public function __construct()
    {
    }

    public function get(string $key): ?string
    {
        $value = $this->props[$key] ?? null;

        if ($value === null) {
            $value = getenv($key);

            if (!is_string($value)) {
                $value = null;
            }
        }

        return $value;
    }

    /**
     * @throws ConfigException
     */
    public function req(string $key): string
    {
        return $this->get($key)
            ?? throw new ConfigException('environment variable not set');
    }
}
