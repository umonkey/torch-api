<?php

declare(strict_types=1);

$staticFiles = [
    '/docs/api.yaml',
    '/docs/index.html',
];

if (in_array($_SERVER['REQUEST_URI'], $staticFiles, true)) {
    return false;
}

require __DIR__ . '/bootstrap-web.php';
