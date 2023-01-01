<?php

declare(strict_types=1);

namespace App\Database\Drivers;

use App\Core\Config;
use App\Database\DatabaseInterface;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Helpers\SqlUtils;
use App\Exceptions\ConfigException;
use Generator;
use PDO;
use PDOException;
use PDOStatement;

class SqliteDriver implements DatabaseInterface
{
    private PDO $conn;

    /**
     * @throws ConfigException
     * @throws DatabaseException
     */
    public function __construct(Config $config)
    {
        $dsn = $config->requireString('sqlite.path');

        try {
            $conn = new PDO($dsn, null, null, static::getConnectionOptions());
            $this->conn = $conn;
        } catch (PDOException $e) {
            throw new DatabaseException(sprintf('connection failed: %s', $e->getMessage()));
        }
    }

    /**
     * @param array<mixed> $query
     * @return array[]|Generator<array<mixed>>
     * @throws DatabaseException
     */
    public function find(string $tableName, array $query): Generator
    {
        [$query, $params] = SqlUtils::buildSelect($tableName, [], $query);

        foreach ($this->fetch($query, $params) as $row) {
            yield $row;
        }
    }

    /**
     * @param array<mixed> $keys
     * @throws DatabaseException
     */
    public function get(string $tableName, array $keys): array
    {
        [$query, $params] = SqlUtils::buildSelect($tableName, [], $keys);

        foreach ($this->fetch($query, $params) as $row) {
            return $row;
        }

        throw new RecordNotFoundException();
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
            throw new DatabaseException($e->getMessage());
        }
    }
}
