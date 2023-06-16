<?php

declare(strict_types=1);

namespace App\Database\Drivers;

use App\Core\Config;
use App\Database\DatabaseInterface;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\DuplicateRecordException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Helpers\SqlUtils;
use App\Exceptions\ConfigException;
use Generator;
use PDO;
use PDOException;
use PDOStatement;

class SqliteDriver implements DatabaseInterface
{
    private const PAGES_TABLE = 'pages';
    private const USERS_TABLE = 'users';

    protected PDO $conn;

    /**
     * @throws ConfigException
     * @throws DatabaseException
     */
    public function __construct(Config $config)
    {
        $dsn = $this->getDSN($config);

        try {
            $conn = new PDO($dsn, null, null, static::getConnectionOptions());
            $this->conn = $conn;
        } catch (PDOException $e) {
            throw new DatabaseException(sprintf('connection failed: %s', $e->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function addPage(array $props): void
    {
        $this->add(self::PAGES_TABLE, $props);
    }

    /**
     * @inheritDoc
     */
    public function getPage(string $id): array
    {
        return $this->get(self::PAGES_TABLE, [
            'id = :id' => [
                ':id' => $id,
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function updatePage(array $props): void
    {
        $this->update(self::PAGES_TABLE, [
            'id = :id' => [
                ':id' => $props['id'] ?? throw new DatabaseException('page id not set'),
            ],
        ], $props);
    }

    /**
     * @inheritDoc
     */
    public function findPages(): Generator
    {
        [$query, $params] = SqlUtils::buildSelect(self::PAGES_TABLE, [], []);

        foreach ($this->fetch($query, $params) as $row) {
            yield $row;
        }
    }

    /**
     * @inheritDoc
     */
    public function addUser(array $props): void
    {
        $this->add(self::USERS_TABLE, $props);
    }

    /**
     * @inheritDoc
     */
    public function getUser(string $id): array
    {
        return $this->get(self::USERS_TABLE, [
            'id = :id' => [
                ':id' => $id,
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function updateUser(array $props): void
    {
        $this->update(self::USERS_TABLE, [
            'id = :id' => [
                ':id' => $props['id'] ?? throw new DatabaseException('user id not set'),
            ],
        ], $props);
    }

    /**
     * @param array<string,mixed> $props
     * @throws DatabaseException
     */
    private function add(string $tableName, array $props): void
    {
        [$query, $params] = SqlUtils::buildInsert($tableName, $props);

        $st = $this->query($query, $params);
        $count = $st->rowCount();

        if ($count === 0) {
            throw new DatabaseException('error inserting a new record');
        }
    }

    /**
     * @param mixed[] $keys
     * @return mixed[]
     * @throws DatabaseException
     */
    private function get(string $tableName, array $keys): array
    {
        [$query, $params] = SqlUtils::buildSelect($tableName, [], $keys);

        foreach ($this->fetch($query, $params) as $row) {
            return $row;
        }

        throw new RecordNotFoundException();
    }

    /**
     * @param mixed[] $keys
     * @param mixed[] $props
     * @throws DatabaseException
     */
    private function update(string $tableName, array $keys, array $props): void
    {
        [$query, $params] = SqlUtils::buildUpdate($tableName, $keys, $props);

        $sth = $this->query($query, $params);
        $count = $sth->rowCount();

        if ($count === 0) {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @throws ConfigException
     */
    protected function getDSN(Config $config): string
    {
        return $config->requireString('sqlite.path');
    }

    /**
     * @return array<mixed>
     */
    protected static function getConnectionOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
    }

    /**
     * @param array<mixed> $params
     * @return array[]|Generator<array<mixed>>
     * @throws DatabaseException
     */
    private function fetch(string $query, array $params): Generator
    {
        $sth = $this->query($query, $params);

        while (($row = $sth->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield $row;
        }
    }

    /**
     * @throws DatabaseException
     */
    private function prepare(string $query): PDOStatement
    {
        try {
            return $this->conn->prepare($query);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @param array<mixed> $params
     * @throws DatabaseException
     */
    private function query(string $query, array $params): PDOStatement
    {
        try {
            $sth = $this->prepare($query);
            $sth->execute($params);
            return $sth;
        } catch (PDOException $e) {
            throw $this->wrapException($e);
        }
    }

    protected function wrapException(PDOException $e): DatabaseException
    {
        $msg = $e->getMessage();

        if (str_contains($msg, 'UNIQUE constraint failed')) {
            return new DuplicateRecordException();
        }

        return new DatabaseException($e->getMessage());
    }
}
