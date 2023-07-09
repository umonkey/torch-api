<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Config\Environment;
use App\Core\Testing\FixtureFactory;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Throwable;

class AbstractTestCase extends TestCase
{
    protected readonly ContainerInterface $container;

    protected readonly Environment $env;

    private readonly FixtureFactory $fixtures;

    public function testEnvironment(): void
    {
        $value = $this->env->get('APP_ENV');
        self::assertNotNull($value, 'APP_ENV not set');
        self::assertEquals('unit_tests', $this->env->get('APP_ENV'), 'APP_ENM must be set to unit_tests, otherwise real resources could be modified.');
    }

    protected function fixture(string $fileName): void
    {
        try {
            $path = $this->fixtures->locate($fileName);
            self::assertNotNull($path, 'fixture not found');

            $this->fixtures->processFile($path);
        } catch (Throwable $e) {
            self::fail(sprintf('error setting up fixture %s: %s', $fileName, $e->getMessage()));
        }
    }

    /**
     * @throws ContainerExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->getContainer();
        $this->env = $this->container->get(Environment::class);
        $this->fixtures = $this->container->get(FixtureFactory::class);
    }

    private function getContainer(): ContainerInterface
    {
        $cb = new ContainerBuilder();
        $cb->addDefinitions(include __DIR__ . '/../../config/dependencies.php');
        return $cb->build();
    }
}
