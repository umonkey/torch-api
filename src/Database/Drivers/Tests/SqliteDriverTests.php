<?php

declare(strict_types=1);

namespace App\Database\Drivers\Tests;

use App\Core\AbstractTestCase;
use App\Core\Config;
use App\Database\Drivers\MemoryDriver;
use App\Database\Drivers\SqliteDriver;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\ConfigException;

class SqliteDriverTests extends AbstractTestCase
{
    private SqliteDriver $driver;

    /**
     * @throws ConfigException
     * @throws DatabaseException
     */
    public function testNoConfig(): void
    {
        $this->expectException(ConfigException::class);

        $config = Config::fromArray([]);
        new SqliteDriver($config);
    }

    /**
     * @throws ConfigException
     * @throws DatabaseException
     */
    public function testBadFile(): void
    {
        $this->expectException(DatabaseException::class);

        $config = Config::fromArray([
            'sqlite.path' => __FILE__,
        ]);

        new SqliteDriver($config);
    }

    /**
     * @throws DatabaseException
     */
    public function testBadInsert(): void
    {
        $this->expectException(DatabaseException::class);

        $this->driver->addUser([
            'id' => 'foobar',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->container->get(MemoryDriver::class);
    }
}
