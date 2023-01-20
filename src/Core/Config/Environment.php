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
        $this->props = $this->load();
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
            ?? throw new ConfigException(sprintf('environment variable "%s" not set', $key));
    }

    /**
     * @return array<string,string>
     * @throws ConfigException
     */
    private function load(): array
    {
        $source = '.env.php';

        if (!file_exists($source)) {
            return [];
        }

        $data = include $source;

        if (!is_array($data)) {
            throw new ConfigException('.env.php must return an array');
        }

        foreach ($data as $k => $v) {
            if (!is_string($k)) {
                throw new ConfigException('environment keys must be strings');
            }

            if (!is_string($v)) {
                throw new ConfigException('environment values must be strings');
            }
        }

        return $data;
    }
}
