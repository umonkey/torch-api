<?php

/**
 * Low level data storage interface.  Wraps the database driver, providing
 * a few highere level no-SQL data access methods.
 */

declare(strict_types=1);

namespace App\Database;

use App\Database\Exceptions\DatabaseException;
use Generator;

interface DatabaseInterface
{
    /**
     * Return a single record, identified by a PK or PK+SK.
     *
     * @param array<string,array<string,mixed>> $keys
     * @return array<string,mixed>
     * @throws DatabaseException
     */
    public function get(string $tableName, array $keys): array;

    /**
     * @param array<mixed> $query
     * @return array[]|Generator<array<mixed>>
     * @throws DatabaseException
     */
    public function find(string $tableName, array $query): Generator;
}