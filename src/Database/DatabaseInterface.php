<?php

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
    public function addPage(array $props): void;

    /**
     * Return a single record, identified by a PK or PK+SK.
     *
     * @return mixed[]
     * @throws DatabaseException
     */
    public function getPage(string $id): array;

    /**
     * @param mixed[] $props
     * @throws DatabaseException
     */
    public function updatePage(array $props): void;

    /**
     * @throws DatabaseException
     */
    public function deletePage(string $id): void;

    /**
     * @return mixed[]|Generator
     * @throws DatabaseException
     */
    public function findPages(): Generator;

    /**
     * @param array<string,mixed> $props
     * @throws DatabaseException
     */
    public function addUser(array $props): void;

    /**
     * Return a single record, identified by a PK or PK+SK.
     *
     * @return mixed[]
     * @throws DatabaseException
     */
    public function getUser(string $id): array;

    /**
     * @param mixed[] $props
     * @throws DatabaseException
     */
    public function updateUser(array $props): void;
}
