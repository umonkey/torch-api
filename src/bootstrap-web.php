<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define('APP_ENV', match (getenv('APP_ENV')) {
    false => 'production',
    default => getenv('APP_ENV'),
});

App\App::run();
