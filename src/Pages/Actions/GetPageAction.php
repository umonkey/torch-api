<?php

declare(strict_types=1);

namespace App\Pages\Actions;

use App\Core\AbstractAction;
use App\Core\QueryObject;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadRequestException;
use App\Exceptions\PageNotFoundException;
use App\Pages\Pages;
use App\Pages\Responders\PageResponder;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class GetPageAction extends AbstractAction
{
    public function __construct(private readonly Pages $pages, private readonly PageResponder $responder)
    {
    }

    /**
     * @throws BadRequestException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query = QueryObject::fromRequest($request);
        $id = $query->requireString('id');

        $page = $this->pages->get($id);

        return $this->responder->respond($response, $page);
    }
}