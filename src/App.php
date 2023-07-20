<?php

declare(strict_types=1);

namespace App;

use App\Core\Container;
use DI\Bridge\Slim\Bridge;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App as SlimApp;

/**
 * @codeCoverageIgnore
 */
class App
{
    /**
     * @throws ContainerExceptionInterface
     */
    public static function run(): void
    {
        self::getApp()->run();
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public static function getApp(): SlimApp
    {
        $container = new Container();

        $app = Bridge::create($container);
        $container->set(ResponseFactoryInterface::class, $app->getResponseFactory());

        include __DIR__ . '/../config/routes.php';
        include __DIR__ . '/../config/middleware.php';

        return $app;
    }
}
