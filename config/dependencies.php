<?php

declare(strict_types=1);

use App\Core\Logging\ConsoleLogger;
use App\Database\DatabaseInterface;
use App\Database\Drivers\SqliteDriver;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    DatabaseInterface::class => function (ContainerInterface $container) {
        return $container->get(SqliteDriver::class);
    },

    LoggerInterface::class => function (ContainerInterface $container) {
        return $container->get(ConsoleLogger::class);
    },
];
