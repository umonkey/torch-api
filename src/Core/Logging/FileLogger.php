<?php

declare(strict_types=1);

namespace App\Core\Logging;

use App\Core\Config;
use App\Exceptions\ConfigException;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class FileLogger extends Logger
{
    /**
     * @throws ConfigException
     * @throws InvalidArgumentException
     **/
    public function __construct(Config $config)
    {
        $groupName = $config->requireString('log.group');
        $fileName = $config->requireString('log.filename');

        parent::__construct($groupName);

        $handler = new StreamHandler($fileName, Logger::DEBUG);
        $this->pushHandler($handler);
    }
}
