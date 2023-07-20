<?php

declare(strict_types=1);

namespace App\Tools;

use App\Core\Config\Environment;
use DI\Container;
use Throwable;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractCommand
{
    /**
     * @param string[] $args
     */
    abstract public function __invoke(array $args): void;

    final public static function run(): void
    {
        require_once __DIR__ . '/../functions.php';

        try {
            $config = include 'config/dependencies.php';
            $container = new Container($config);

            $env = $container->get(Environment::class);
            self::setupConstants($env);

            $handler = $container->get(get_called_class());

            $args = array_slice($GLOBALS['argv'], 1);
            $handler($args);
        } catch (Throwable $e) {
            fprintf(STDERR, "%s: %s\n", get_class($e), $e->getMessage());
            fprintf(STDERR, "File %s line %d\n", $e->getFile(), $e->getLine());

            ob_start();
            debug_print_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
            $stack = ob_get_clean();

            fprintf(STDERR, "%s\n", $stack);
            exit(1);
        }
    }

    private static function setupConstants(Environment $env): void
    {
        if (!defined('APP_ENV')) {
            $value = $env->get('APP_ENV') ?? 'local';
            define('APP_ENV', $value);
        }
    }
}
