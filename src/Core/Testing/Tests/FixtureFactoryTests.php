<?php

declare(strict_types=1);

namespace App\Core\Testing\Tests;

use App\Core\AbstractTestCase;
use App\Core\Testing\FixtureFactory;
use RuntimeException;

class FixtureFactoryTests extends AbstractTestCase
{
    private readonly FixtureFactory $factory;

    public function testFileNotFound(): void
    {
        $path = $this->factory->locate('foobar.yaml');
        self::assertEquals(null, $path);
    }

    /**
     * @throws RuntimeException
     */
    public function testUnknownTable(): void
    {
        self::expectException(RuntimeException::class);

        $path = $this->factory->locate('001.yaml');
        self::assertNotNull($path);

        $this->factory->processFile($path);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->container->get(FixtureFactory::class);
    }
}
