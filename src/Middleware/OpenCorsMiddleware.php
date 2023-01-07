<?php

/**
 * Reply to all OPTIONS requests without having to add routes.
 **/

declare(strict_types=1);

namespace App\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class OpenCorsMiddleware
{
    public function __construct(private readonly ResponseFactoryInterface $rf)
    {
    }

    /**
     * @throws InvalidArgumentException
     **/
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = $this->rf->createResponse()
                ->withStatus(200)
                ->withHeader('Cache-Control', 'max-age=3600');
        } else {
            $response = $handler->handle($request);
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Authorization,DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Postman-Token')
            ->withHeader('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, HEAD, OPTIONS, PATCH')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
