<?php

declare(strict_types=1);

namespace App;

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App as SlimApp;

class App
{
    public static function run(): void
    {
        self::getApp()->run();
    }

    public static function getApp(): SlimApp
    {
        $cb = new ContainerBuilder();
        $cb->useAnnotations(false);
        $cb->addDefinitions(include __DIR__ . '/../config/dependencies.php');
        $container = $cb->build();

        $app = Bridge::create($container);
        $container->set(ResponseFactoryInterface::class, $app->getResponseFactory());

        include __DIR__ . '/../config/routes.php';
        include __DIR__ . '/../config/middleware.php';

        return $app;
    }
}
