<?php

/**
 * @var Slim\App $app
 **/

declare(strict_types=1);

use App\Index\Actions\IndexAction;
use App\Pages\Actions\GetPageAction;
use App\Pages\Actions\PutPageAction;

$app->get('/', IndexAction::class);

$app->get('/pages/{id}', GetPageAction::class);
$app->put('/pages/{id}', PutPageAction::class);
