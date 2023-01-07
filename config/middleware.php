<?php

/**
 * Add middleware helpers.
 *
 * @var Slim\App $app Application to add the middleware to.
 * @var Redgifs\Helpers\Config $config Configuration container.
 **/

declare(strict_types=1);

use App\Middleware\ErrorHandlerMiddleware;
use App\Middleware\OpenCorsMiddleware;

// Must go almost last to catch errors in all other middleware.
$app->add(ErrorHandlerMiddleware::class);

// Must go last to add CORS headers to error messages.
// which need to be converted from exceptions by ErrorHandler.
$app->add(OpenCorsMiddleware::class);
