<?php

declare(strict_types=1);

namespace App\Database\Drivers;

use App\Core\Config;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\ConfigException;

class MemoryDriver extends SqliteDriver
{
    /**
     * @throws ConfigException
     * @throws DatabaseException
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->initSchema();
    }

    protected function getDSN(Config $config): string
    {
        return 'sqlite::memory:';
    }

    /**
     * @throws DatabaseException
     * @codeCoverageIgnore
     */
    private function initSchema(): void
    {
        $fn = __DIR__ . '/../../../config/schema-sqlite.sql';

        if (!is_readable($fn)) {
            throw new DatabaseException('schema file not found');
        }

        $query = file_get_contents($fn);

        if (!is_string($query)) {
            throw new DatabaseException('error reading schema file');
        }

        $this->conn->exec($query);
    }
}
