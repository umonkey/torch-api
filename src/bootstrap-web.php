<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define('APP_ENV', getenv('APP_ENV'));

App\App::run();
