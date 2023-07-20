<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Config\Environment;
use DI\Container as Base;
use Psr\Container\ContainerExceptionInterface;

class Container extends Base
{
    private const CONSTANTS = [
        'APP_ENV' => 'local',
    ];

    /**
     * @throws ContainerExceptionInterface
     */
    public function __construct()
    {
        $config = include 'config/dependencies.php';
        parent::__construct($config);

        $this->setupEnvironment();
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function setupEnvironment(): void
    {
        $env = $this->get(Environment::class);

        foreach (self::CONSTANTS as $k => $v) {
            if (!defined($k)) {
                define($k, $env->get($k) ?? $v);
            }
        }
    }
}
