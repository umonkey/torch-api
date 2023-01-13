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
     * @param array<string,mixed> $props
     * @throws DatabaseException
     */
    public function add(string $tableName, array $props): void;

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

    /**
     * Update a single record, identified by a PK or PK+SK.
     *
     * @param array<string,array<string,mixed>> $keys
     * @param array<string,mixed> $props
     * @throws DatabaseException
     */
    public function update(string $tableName, array $keys, array $props): void;
}
