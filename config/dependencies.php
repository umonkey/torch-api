<?php

declare(strict_types=1);

use App\Auth\AuthInterface;
use App\Auth\TokenAuthClient;
use App\Core\Config;
use App\Core\Config\Environment;
use App\Core\Logging\ConsoleLogger;
use App\Core\Logging\FileLogger;
use App\Database\DatabaseInterface;
use App\Database\Drivers\DynamoDbDriver;
use App\Database\Drivers\MemoryDriver;
use App\Database\Drivers\SqliteDriver;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    AuthInterface::class => function (ContainerInterface $container): AuthInterface {
        return $container->get(TokenAuthClient::class);
    },

    DatabaseInterface::class => function (ContainerInterface $container): DatabaseInterface {
        $env = $container->get(Environment::class);
        $config = $container->get(Config::class);

        $className = match ($env->get('APP_ENV')) {
            'unit_tests' => MemoryDriver::class,

            default => match ($config->getString('db.driver')) {
                'dynamo' => DynamoDbDriver::class,
                default => SqliteDriver::class,
            },
        };

        return $container->get($className);
    },

    LoggerInterface::class => function (ContainerInterface $container): LoggerInterface {
        $env = $container->get(Environment::class);

        return match ($env->get('APP_ENV')) {
            'unit_tests' => $container->get(FileLogger::class),
            default => $container->get(ConsoleLogger::class),
        };
    },
];
