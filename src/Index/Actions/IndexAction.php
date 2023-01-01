<?php

declare(strict_types=1);

namespace App\Index\Actions;

use App\Core\AbstractAction;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class IndexAction extends AbstractAction
{
    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('It Works!');

        return $response->withHeader('Content-Type', 'text/plain');
    }
}
