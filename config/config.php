<?php

/**
 * @var App\Core\Config\Environment $env
 */

declare(strict_types=1);

return [
    'jwt.algo' => 'HS256',
    'jwt.secret' => $env->get('JWT_SECRET'),

    'log.group' => 'api',

    'sqlite.path' => $env->get('SQLITE_PATH'),

    'log.filename' => 'var/errors.log',
];
