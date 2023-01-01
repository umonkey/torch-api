<?php

declare(strict_types=1);

return [
    'log.group' => 'api',

    'sqlite.path' => sprintf('sqlite://%s', __DIR__ . '/../var/database.sqlite'),
];
