<?php

/**
 * @var App\Core\Config\Environment $env
 */

declare(strict_types=1);

return [
    'jwt.algo' => 'HS256',
    'jwt.secret' => $env->req('JWT_SECRET'),

    'log.group' => 'api',

    'sqlite.path' => sprintf('sqlite://%s', __DIR__ . '/../var/database.sqlite'),
];
