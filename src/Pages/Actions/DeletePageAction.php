<?php

declare(strict_types=1);

namespace App\Pages\Actions;

use App\Auth\AuthInterface;
use App\Core\AbstractAction;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadRequestException;
use App\Exceptions\PageNotFoundException;
use App\Pages\Pages;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeletePageAction extends AbstractAction
{
    public function __construct(private readonly AuthInterface $auth, private readonly Pages $pages)
    {
    }

    /**
     * @throws BadRequestException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws PageNotFoundException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $this->auth->authenticate($request);
        $id = $this->getRouteArg($request, 'id');

        $this->pages->delete($id, $user);

        return $response->withStatus(202);
    }
}
