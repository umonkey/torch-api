<?php

/**
 * @var Slim\App $app
 **/

declare(strict_types=1);

use App\Index\Actions\IndexAction;
use App\OAuth\Actions\PasswordGrantAction;
use App\Pages\Actions\GetPageAction;
use App\Pages\Actions\PutPageAction;

$app->get('/', IndexAction::class);

$app->post('/oauth/password', PasswordGrantAction::class);

$app->get('/pages/{id}', GetPageAction::class);
$app->put('/pages/{id}', PutPageAction::class);
