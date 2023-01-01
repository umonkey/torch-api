<?php

require_once 'vendor/squizlabs/php_codesniffer/src/Sniffs/Sniff.php';
require_once 'vendor/squizlabs/php_codesniffer/src/Util/Tokens.php';

if (!defined('APP_ROOT')) {
    define('APP_ROOT', getcwd());
}

function __get_env(): string
{
    $value = getenv('APP_ENV');

    if ($value === false) {
        $value = 'production';
    }

    return $value;
}

function __get_stage(): string
{
    $value = getenv('APP_STAGE');

    if ($value === false) {
        if (file_exists('.git/HEAD')) {
            $head = trim(file_get_contents('.git/HEAD'));

            $value = match ($head) {
                'ref: refs/heads/master' => 'production',
                default => 'staging',
            };
        } else {
            $value = 'production';
        }
    }

    return $value;
}


define('APP_ENV', __get_env());
define('APP_STAGE', __get_stage());
