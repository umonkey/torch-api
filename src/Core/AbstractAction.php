<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\BadRequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractAction
{
    abstract public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;

    /**
     * @throws BadRequestException
     */
    protected function getRouteArg(ServerRequestInterface $request, string $name): string
    {
        $args = $request->getAttribute('__route__');

        if ($args === null) {
            throw new BadRequestException('route args not set');
        }

        $values = $args->getArguments();

        if (!array_key_exists($name, $values)) {
            throw new BadRequestException(sprintf('route arg "%s" not set', $name));
        }

        return $values[$name];
    }
}
