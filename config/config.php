<?php

/**
 * @var App\Core\Config\Environment $env
 */

declare(strict_types=1);

return [
    'aws.region' => $env->get('AWS_REGION'),
    'aws.key' => $env->get('AWS_ACCESS_KEY_ID'),
    'aws.secret' => $env->get('AWS_SECRET_ACCESS_KEY'),

    'jwt.algo' => 'HS256',

    'jwt.secret' => match (APP_ENV) {
        'unit_tests' => 'secret',
        default => $env->get('JWT_SECRET'),
    },

    'log.group' => 'api',

    'sqlite.path' => $env->get('SQLITE_PATH'),

    'log.filename' => 'var/errors.log',
];
