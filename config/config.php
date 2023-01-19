<?php

declare(strict_types=1);

return [
    'jwt.algo' => 'HS256',
    'jwt.secret' => getenv('JWT_SECRET'),

    'log.group' => 'api',

    'sqlite.path' => sprintf('sqlite://%s', __DIR__ . '/../var/database.sqlite'),
];
