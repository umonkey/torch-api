<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Config\Environment;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class AbstractTestCase extends TestCase
{
    protected readonly ContainerInterface $container;

    protected readonly Environment $env;

    public function testEnvironment(): void
    {
        $value = $this->env->get('APP_ENV');
        self::assertNotNull($value, 'APP_ENV not set');
        self::assertEquals('unit_tests', $this->env->get('APP_ENV'), 'APP_ENM must be set to unit_tests, otherwise real resources could be modified.');
    }

    /**
     * @throws ContainerExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->getContainer();
        $this->env = $this->container->get(Environment::class);
    }

    private function getContainer(): ContainerInterface
    {
        $cb = new ContainerBuilder();
        $cb->addDefinitions(include __DIR__ . '/../../config/dependencies.php');
        return $cb->build();
    }
}
