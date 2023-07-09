<?php

declare(strict_types=1);

use App\Auth\AuthInterface;
use App\Auth\TokenAuthClient;
use App\Core\Config\Environment;
use App\Core\Logging\ConsoleLogger;
use App\Core\Logging\FileLogger;
use App\Database\DatabaseInterface;
use App\Database\Drivers\MemoryDriver;
use App\Database\Drivers\SqliteDriver;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    AuthInterface::class => function (ContainerInterface $container) {
        return $container->get(TokenAuthClient::class);
    },

    DatabaseInterface::class => function (ContainerInterface $container) {
        $env = $container->get(Environment::class);

        $className = match ($env->get('APP_ENV')) {
            'unit_tests' => MemoryDriver::class,
            default => SqliteDriver::class,
        };

        return $container->get($className);
    },

    LoggerInterface::class => function (ContainerInterface $container) {
        $env = $container->get(Environment::class);

        return match ($env->get('APP_ENV')) {
            'unit_tests' => $container->get(FileLogger::class),
            default => $container->get(ConsoleLogger::class),
        };
    },
];
