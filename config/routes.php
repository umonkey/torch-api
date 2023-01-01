<?php

/**
 * @var Slim\App $app
 **/

declare(strict_types=1);

use App\Index\Actions\IndexAction;
use App\Pages\Actions\GetPageAction;

$app->get('/', IndexAction::class);

$app->get('/pages', GetPageAction::class);
