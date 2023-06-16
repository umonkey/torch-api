<?php

declare(strict_types=1);

namespace App\Pages\Actions;

use App\Auth\AuthInterface;
use App\Core\AbstractAction;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadRequestException;
use App\Exceptions\BadResponseException;
use App\Exceptions\PageNotFoundException;
use App\Pages\Pages;
use App\Pages\Responders\PageResponder;
use InvalidArgumentException;
use JsonException;
use League\CommonMark\Exception\CommonMarkException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class GetPageAction extends AbstractAction
{
    public function __construct(
        private readonly AuthInterface $auth,
        private readonly Pages $pages,
        private readonly PageResponder $responder,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws BadResponseException
     * @throws CommonMarkException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $this->auth->authenticate($request);

        $id = $this->getRouteArg($request, 'id');

        $page = $this->pages->get($id, $user);

        return $this->responder->respond($page);
    }
}
