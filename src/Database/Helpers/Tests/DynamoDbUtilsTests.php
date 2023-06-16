<?php

declare(strict_types=1);

namespace App\Database\Helpers\Tests;

use App\Core\AbstractTestCase;
use App\Database\Exceptions\DatabaseException;
use App\Database\Helpers\DynamoDbUtils;

class DynamoDbUtilsTests extends AbstractTestCase
{
    /**
     * @throws DatabaseException
     */
    public function testWrap(): void
    {
        $res = DynamoDbUtils::wrap([
            'int' => 1,
            'string' => 'foobar',
            'bool' => true,
            'null' => null,
            'map' => [
                'foo' => 'bar',
            ],
            'strings' => [
                'foo',
                'bar',
            ],
            'numbers' => [
                1,
                1.1,
            ],
        ]);

        self::assertEquals([
            'int' => [
                'N' => '1',
            ],
            'string' => [
                'S' => 'foobar',
            ],
            'bool' => [
                'BOOL' => true,
            ],
            'null' => [
                'NULL' => true,
            ],
            'map' => [
                'M' => [
                    'foo' => [
                        'S' => 'bar',
                    ],
                ],
            ],
            'strings' => [
                'SS' => [
                    'foo',
                    'bar',
                ],
            ],
            'numbers' => [
                'NS' => [
                    '1',
                    '1.1',
                ],
            ],
        ], $res);
    }

    public function testUnwrap(): void
    {
        $res = DynamoDbUtils::unwrap([
            'int' => [
                'N' => '1',
            ],
            'string' => [
                'S' => 'foobar',
            ],
            'bool' => [
                'BOOL' => true,
            ],
            'null' => [
                'NULL' => true,
            ],
            'strings' => [
                'SS' => [
                    'first',
                    'second',
                ],
            ],
            'map' => [
                'M' => [
                    'key' => [
                        'S' => 'value',
                    ],
                ],
            ],
            'int-num' => [
                'N' => '1',
            ],
            'float-num' => [
                'N' => '1.1',
            ],
            'binary' => [
                'B' => '\x00\x01',
            ],
            'num-set' => [
                'NS' => [
                    '1',
                    '1.1',
                ],
            ],
        ]);

        self::assertEquals([
            'int' => 1,
            'string' => 'foobar',
            'strings' => [
                'first',
                'second',
            ],
            'bool' => true,
            'null' => null,
            'map' => [
                'key' => 'value',
            ],
            'int-num' => 1,
            'float-num' => 1.1,
            'binary' => '\x00\x01',
            'num-set' => [1, 1.1],
        ], $res);
    }

    public function testUnwrapUnknown(): void
    {
        $this->expectException(DatabaseException::class);

        DynamoDbUtils::unwrap([
            'key' => [
                'XYZ' => 'foobar',
            ],
        ]);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildGetQueryPK(): void
    {
        $query = DynamoDbUtils::buildGetQuery('someTable', [
            'pk' => 'foobar',
        ]);

        self::assertEquals([
            'TableName' => 'someTable',
            'Key' => [
                'pk' => [
                    'S' => 'foobar',
                ],
            ],
        ], $query);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildGetQuerySK(): void
    {
        $query = DynamoDbUtils::buildGetQuery('someTable', [
            'pk' => 'foobar',
            'sk' => 'snafu',
        ]);

        self::assertEquals([
            'TableName' => 'someTable',
            'Key' => [
                'pk' => [
                    'S' => 'foobar',
                ],
                'sk' => [
                    'S' => 'snafu',
                ],
            ],
        ], $query);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildPutQueryPK(): void
    {
        $query = DynamoDbUtils::buildPutQuery('someTable', [
            'id' => 'foobar',
        ], [
            'name' => 'Foobar2000',
        ]);

        self::assertEquals([
            'TableName' => 'someTable',
            'Item' => [
                'id' => [
                    'S' => 'foobar',
                ],
                'name' => [
                    'S' => 'Foobar2000',
                ],
            ],
            'ConditionExpression' => 'attribute_not_exists(#hash)',
            'ExpressionAttributeNames' => [
                '#hash' => 'id',
            ],
        ], $query);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildPutQuerySK(): void
    {
        $query = DynamoDbUtils::buildPutQuery('someTable', [
            'id' => 'foobar',
            'subid' => 'snafu',
        ], [
            'name' => 'Foobar2000',
        ]);

        self::assertEquals([
            'TableName' => 'someTable',
            'Item' => [
                'id' => [
                    'S' => 'foobar',
                ],
                'subid' => [
                    'S' => 'snafu',
                ],
                'name' => [
                    'S' => 'Foobar2000',
                ],
            ],
            'ConditionExpression' => 'attribute_not_exists(#hash) AND attribute_not_exists(#range)',
            'ExpressionAttributeNames' => [
                '#hash' => 'id',
                '#range' => 'subid',
            ],
        ], $query);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildUpdateQueryPK(): void
    {
        $query = DynamoDbUtils::buildUpdateQuery('someTable', [
            'id' => 'foobar',
        ], [
            'value' => 'hello',
        ]);

        self::assertEquals([
            'TableName' => 'someTable',
            'UpdateExpression' => 'SET #value = :value',
            'ConditionExpression' => 'attribute_exists(#hashkey)',
            'ExpressionAttributeNames' => [
                '#hashkey' => 'id',
                '#value' => 'value',
            ],
            'Key' => [
                'id' => [
                    'S' => 'foobar',
                ],
            ],
            'ExpressionAttributeValues' => [
                ':value' => [
                    'S' => 'hello',
                ],
            ],
        ], $query);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildUpdateQuerySK(): void
    {
        $query = DynamoDbUtils::buildUpdateQuery('someTable', [
            'id' => 'foobar',
            'subid' => 'snafu',
        ], [
            'value' => 'hello',
        ]);

        self::assertEquals([
            'TableName' => 'someTable',
            'UpdateExpression' => 'SET #value = :value',
            'ConditionExpression' => 'attribute_exists(#hashkey) AND attribute_exists(#rangekey)',
            'ExpressionAttributeNames' => [
                '#hashkey' => 'id',
                '#rangekey' => 'subid',
                '#value' => 'value',
            ],
            'Key' => [
                'id' => [
                    'S' => 'foobar',
                ],
                'subid' => [
                    'S' => 'snafu',
                ],
            ],
            'ExpressionAttributeValues' => [
                ':value' => [
                    'S' => 'hello',
                ],
            ],
        ], $query);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildUpdateQueryRemove(): void
    {
        $query = DynamoDbUtils::buildUpdateQuery('someTable', [
            'id' => 'foobar',
            'subid' => 'snafu',
        ], [
            'value' => null,
        ]);

        self::assertEquals([
            'TableName' => 'someTable',
            'UpdateExpression' => 'REMOVE #value',
            'ConditionExpression' => 'attribute_exists(#hashkey) AND attribute_exists(#rangekey)',
            'ExpressionAttributeNames' => [
                '#hashkey' => 'id',
                '#rangekey' => 'subid',
                '#value' => 'value',
            ],
            'Key' => [
                'id' => [
                    'S' => 'foobar',
                ],
                'subid' => [
                    'S' => 'snafu',
                ],
            ],
        ], $query);
    }

    public function testBuildScanQuery(): void
    {
        $query = DynamoDbUtils::buildScanQuery('someTable', 'some-cursor');

        self::assertEquals([
            'TableName' => 'someTable',
            'ExclusiveStartKey' => 'some-cursor',
        ], $query);
    }

    public function testBuildScanQueryNoCursor(): void
    {
        $query = DynamoDbUtils::buildScanQuery('someTable', null);

        self::assertEquals([
            'TableName' => 'someTable',
        ], $query);
    }
}
