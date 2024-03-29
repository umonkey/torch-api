<?php

/**
 * The authentication client that uses JWT tokens.
 */

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Exceptions\AuthorizationMissingException;
use App\Auth\Exceptions\BadTokenFormatException;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\UserRepository;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class TokenAuthClient implements AuthInterface
{
    public function __construct(
        private readonly TokenFactory $token,
        private readonly LoggerInterface $logger,
        private readonly UserRepository $users
    ) {
    }

    /**
     * Extract user info from the request.
     *
     * Looks for the authorization header, extracts the token, reads the user id
     * from the `sub' claim, loads the user entity.  On any error, throws an exception.
     *
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    public function authenticate(ServerRequestInterface $request): UserEntity
    {
        $userId = $this->getUserId($request);

        try {
            return $this->users->get($userId);
        } catch (RecordNotFoundException) {
            $this->logger->warning(sprintf('User %s not found, cannot authenticate.', $userId));
            throw new UserNotFoundException();
        }
    }

    /**
     * @throws UnauthorizedException
     */
    private function getUserId(ServerRequestInterface $request): string
    {
        $token = $this->getToken($request);
        $payload = $this->token->decode($token);

        return $payload['sub']
            ?? throw new UnauthorizedException('no sub in the token');
    }

    /**
     * @throws UnauthorizedException
     */
    private function getToken(ServerRequestInterface $request): string
    {
        $value = $request->getHeaderLine('authorization');

        if ($value === '') {
            throw new AuthorizationMissingException();
        }

        $parts = explode(' ', $value, 2);

        if (count($parts) !== 2) {
            throw new BadTokenFormatException();
        }

        if (mb_strtolower($parts[0]) !== 'bearer') {
            throw new BadTokenFormatException();
        }

        return $parts[1];
    }
}
