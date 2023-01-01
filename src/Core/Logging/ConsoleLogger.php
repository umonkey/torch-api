<?php

declare(strict_types=1);

namespace App\Core\Logging;

use App\Core\Config;
use App\Exceptions\ConfigException;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

class ConsoleLogger extends Logger
{
    /**
     * @throws ConfigException
     **/
    public function __construct(Config $config)
    {
        $groupName = $config->requireString('log.group');
        $level = $config->getInt('log.level', Logger::DEBUG);

        parent::__construct($groupName);

        /** @phpstan-ignore-next-line */
        $handler = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $level);
        $this->pushHandler($handler);
    }
}
