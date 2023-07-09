<?php

declare(strict_types=1);

namespace App\Pages\Actions;

use App\Auth\AuthInterface;
use App\Core\AbstractAction;
use App\Core\JsonResponse;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadResponseException;
use App\Pages\ListPages;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListPagesAction extends AbstractAction
{
    public function __construct(private readonly AuthInterface $auth, private readonly ListPages $handler)
    {
    }

    /**
     * @throws BadResponseException
     * @throws DatabaseException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $this->auth->authenticate($request);

        $pages = $this->handler->getPages($user);
        return new JsonResponse($pages);
    }
}
