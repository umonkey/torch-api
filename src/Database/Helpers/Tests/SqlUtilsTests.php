<?php

declare(strict_types=1);

namespace App\Database\Helpers\Tests;

use App\Core\AbstractTestCase;
use App\Database\Exceptions\DatabaseException;
use App\Database\Helpers\SqlUtils;

class SqlUtilsTests extends AbstractTestCase
{
    /**
     * @throws DatabaseException
     */
    public function testBuildAltWhere(): void
    {
        $params = [];

        $query = SqlUtils::buildAltWhere($params, [
            '#user = :userName' => [
                ':userName' => 'phpunit',
                '#user' => 'user',
            ],
        ]);

        self::assertEquals(' WHERE `user` = :userName', $query);

        self::assertEquals([
            ':userName' => 'phpunit',
        ], $params);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildDelete(): void
    {
        $res = SqlUtils::buildDelete('foobar', [
            'id = :id' => [
                ':id' => 'foo',
            ],
        ]);

        self::assertEquals(2, count($res));

        self::assertEquals("DELETE FROM `foobar` WHERE `id` = :id", $res[0]);

        self::assertEquals([
            ':id' => 'foo',
        ], $res[1]);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildIncrement(): void
    {
        $res = SqlUtils::buildIncrement('foobar', [
            'id = :id' => [
                ':id' => 'foo',
            ],
        ], [
            'views' => 2,
        ]);

        self::assertEquals(2, count($res));

        self::assertEquals("UPDATE `foobar` SET `views` = COALESCE(`views`, 0) + :views WHERE `id` = :id", $res[0]);

        self::assertEquals([
            ':views' => 2,
            ':id' => 'foo',
        ], $res[1]);
    }

    public function testBuildInsert(): void
    {
        $res = SqlUtils::buildInsert('foobar', [
            'id' => 'foo',
            'views' => 2,
        ]);

        self::assertEquals(2, count($res));

        self::assertEquals("INSERT INTO `foobar` (`id`, `views`) VALUES (:id, :views)", $res[0]);

        self::assertEquals([
            ':id' => 'foo',
            ':views' => 2,
        ], $res[1]);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildSelectAllFields(): void
    {
        $res = SqlUtils::buildSelect('foobar', [], [
            'foo = :foo' => [
                ':foo' => 'bar',
            ],
        ]);

        self::assertEquals(2, count($res));

        self::assertEquals("SELECT * FROM `foobar` WHERE `foo` = :foo", $res[0]);
        self::assertEquals([':foo' => 'bar'], $res[1]);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildSelectCertainFields(): void
    {
        $res = SqlUtils::buildSelect('foobar', [
            'foo',
            'bar',
        ], [
            'foo = :foo' => [
                ':foo' => 'bar',
            ],
        ]);

        self::assertEquals(2, count($res));

        self::assertEquals("SELECT `foo`, `bar` FROM `foobar` WHERE `foo` = :foo", $res[0]);

        self::assertEquals([
            ':foo' => 'bar',
        ], $res[1]);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildSelectCount(): void
    {
        $res = SqlUtils::buildSelectCount('foobar', [
            'foo = :foo' => [
                ':foo' => 'bar',
            ],
        ]);

        self::assertEquals(2, count($res));

        self::assertEquals("SELECT COUNT(1) AS `count` FROM `foobar` WHERE `foo` = :foo", $res[0]);
        self::assertEquals([':foo' => 'bar'], $res[1]);
    }

    /**
     * @throws DatabaseException
     */
    public function testBuildUpdate(): void
    {
        $res = SqlUtils::buildUpdate('foobar', [
            'id = :id' => [
                ':id' => 'foo',
            ],
        ], [
            'views' => 2,
        ]);

        self::assertEquals(2, count($res));

        self::assertEquals("UPDATE `foobar` SET `views` = :views WHERE `id` = :id", $res[0]);

        self::assertEquals([
            ':views' => 2,
            ':id' => 'foo',
        ], $res[1]);
    }
}
