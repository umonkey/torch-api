<?php

/**
 * @var Slim\App $app
 **/

declare(strict_types=1);

use App\Index\Actions\IndexAction;
use App\OAuth\Actions\PasswordGrantAction;
use App\Pages\Actions\DeletePageAction;
use App\Pages\Actions\GetPageAction;
use App\Pages\Actions\PutPageAction;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function (RouteCollectorProxy $group) {
    $group->get('/', IndexAction::class);

    $group->post('/oauth/password', PasswordGrantAction::class);

    $group->get('/pages/{id}', GetPageAction::class);
    $group->put('/pages/{id}', PutPageAction::class);
    $group->delete('/pages/{id}', DeletePageAction::class);
});
