<?php

declare(strict_types=1);

namespace App\Database\Drivers;

use App\Core\Config;
use App\Database\DatabaseInterface;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\DuplicateRecordException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Helpers\DynamoDbUtils;
use App\Exceptions\ConfigException;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Generator;
use InvalidArgumentException;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class DynamoDbDriver implements DatabaseInterface
{
    private const PAGES_TABLE = 'pages';
    private const USERS_TABLE = 'users';

    /**
     * @var mixed[]
     */
    private array $config;

    private ?DynamoDbClient $client = null;

    private ?string $tablePrefix = null;

    /**
     * @throws ConfigException
     */
    public function __construct(Config $config)
    {
        $this->config = $config->getArray('dynamodb.config') ?? [
            'region' => $config->getString('aws.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => $config->requireString('aws.key'),
                'secret' => $config->requireString('aws.secret'),
            ],
            'http' => [
                'timeout' => $config->getInt('dynamodb.request-timeout') ?? 5,
                'connect_timeout' => $config->getInt('dynamodb.connect-timeout') ?? 1,
            ],
        ];

        $this->tablePrefix = $config->getString('dynamodb.table-prefix');
    }

    /**
     * @throws DatabaseException
     */
    public function addPage(array $props): void
    {
        try {
            $query = DynamoDbUtils::buildPutQuery($this->wrapTableName(self::PAGES_TABLE), [
                'id' => $props['id'] ?? throw new DatabaseException('page id not set'),
            ], $props);

            $this->getClient()->putItem($query);
        } catch (DynamoDbException $e) {
            throw match ($e->getAwsErrorCode()) {
                'ConditionalCheckFailedException' => new DuplicateRecordException(),
                default => self::wrapException($e),
            };
        } catch (DatabaseException $e) {
            throw $e;
        } catch (Throwable $e) {
            self::wrapException($e);
        }
    }

    /**
     * @return mixed[]
     * @throws DatabaseException
     */
    public function getPage(string $id): array
    {
        try {
            $query = DynamoDbUtils::buildGetQuery($this->wrapTableName(self::PAGES_TABLE), [
                'id' => $id,
            ]);

            $res = $this->getClient()->getItem($query);

            if (isset($res['Item'])) {
                return DynamoDbUtils::unwrap($res['Item']);
            }

            throw new RecordNotFoundException();
        } catch (DynamoDbException $e) {
            match ($e->getAwsErrorCode()) {
                'ConditionalCheckFailedException' => throw new RecordNotFoundException(),
                default => self::wrapException($e),
            };
        } catch (DatabaseException $e) {
            throw $e;
        } catch (Throwable $e) {
            self::wrapException($e);
        }
    }

    /**
     * @param mixed[] $props
     * @throws DatabaseException
     */
    public function updatePage(array $props): void
    {
        try {
            $query = DynamoDbUtils::buildUpdateQuery($this->wrapTableName(self::PAGES_TABLE), [
                'id' => $props['id'] ?? throw new DatabaseException('page id not set'),
            ], $props);

            $this->getClient()->updateItem($query);
        } catch (DynamoDbException $e) {
            throw match ($e->getAwsErrorCode()) {
                'ConditionalCheckFailedException' => new RecordNotFoundException(),
                default => self::wrapException($e),
            };
        } catch (DatabaseException $e) {
            throw $e;
        } catch (Throwable $e) {
            self::wrapException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function findPages(): Generator
    {
        try {
            $cursor = null;

            do {
                $query = DynamoDbUtils::buildScanQuery($this->wrapTableName(self::PAGES_TABLE), $cursor);

                $res = $this->getClient()->scan($query);

                if (isset($res['Items'])) {
                    foreach ($res['Items'] as $item) {
                        yield DynamoDbUtils::unwrap($item);
                    }
                }

                $cursor = $res['LastEvaluatedKey'] ?? null;
            } while ($cursor !== null);
        } catch (DatabaseException $e) {
            throw $e;
        } catch (Throwable $e) {
            self::wrapException($e);
        }
    }

    /**
     * @throws DatabaseException
     */
    public function addUser(array $props): void
    {
        $query = DynamoDbUtils::buildPutQuery($this->wrapTableName(self::USERS_TABLE), [
            'id' => $props['id'] ?? throw new DatabaseException('user id not set'),
        ], $props);

        try {
            $this->getClient()->putItem($query);
        } catch (DynamoDbException $e) {
            throw match ($e->getAwsErrorCode()) {
                'ConditionalCheckFailedException' => new DuplicateRecordException(),
                default => self::wrapException($e),
            };
        } catch (DatabaseException $e) {
            throw $e;
        } catch (Throwable $e) {
            self::wrapException($e);
        }
    }

    /**
     * @return mixed[]
     * @throws DatabaseException
     */
    public function getUser(string $id): array
    {
        try {
            $query = DynamoDbUtils::buildGetQuery($this->wrapTableName(self::USERS_TABLE), [
                'id' => $id,
            ]);

            $res = $this->getClient()->getItem($query);

            if (isset($res['Item'])) {
                return DynamoDbUtils::unwrap($res['Item']);
            }

            throw new RecordNotFoundException();
        } catch (DatabaseException $e) {
            throw $e;
        } catch (Throwable $e) {
            self::wrapException($e);
        }
    }

    /**
     * @param mixed[] $props
     * @throws DatabaseException
     */
    public function updateUser(array $props): void
    {
        try {
            $query = DynamoDbUtils::buildUpdateQuery($this->wrapTableName(self::USERS_TABLE), [
                'id' => $props['id'] ?? throw new DatabaseException('user id not set'),
            ], $props);

            $this->getClient()->updateItem($query);
        } catch (DynamoDbException $e) {
            throw match ($e->getAwsErrorCode()) {
                'ConditionalCheckFailedException' => new RecordNotFoundException(),
                default => self::wrapException($e),
            };
        } catch (DatabaseException $e) {
            throw $e;
        } catch (Throwable $e) {
            self::wrapException($e);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getClient(): DynamoDbClient
    {
        if ($this->client === null) {
            $sdk = new \Aws\Sdk($this->config);
            $this->client = $sdk->createDynamoDb();
        }

        return $this->client;
    }

    private function wrapTableName(string $tableName): string
    {
        if ($this->tablePrefix !== null) {
            $tableName = $this->tablePrefix . $tableName;
        }

        return $tableName;
    }

    /**
     * @return never
     * @throws DatabaseException
     */
    private static function wrapException(Throwable $e): DatabaseException
    {
        throw new DatabaseException('command failed');
    }
}
