<?php

declare(strict_types=1);

namespace App\OAuth\Actions;

use App\Core\AbstractAction;
use App\Core\FormObject;
use App\Core\Responders\JsonResponder;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use App\OAuth\OAuth;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class PasswordGrantAction extends AbstractAction
{
    public function __construct(private readonly JsonResponder $responder, private readonly OAuth $oauth)
    {
    }

    /**
     * @throws BadRequestException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     * @throws UnauthorizedException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $form = FormObject::fromRequest($request);

        $grantType = $form->requireString('grant_type');
        $userName = $form->requireString('username');
        $password = $form->requireString('password');

        if ($grantType !== 'password') {
            throw new BadRequestException('wrong grant type');
        }

        $token = $this->oauth->authorize($userName, $password);

        return $this->responder->respond($response, $token);
    }
}
