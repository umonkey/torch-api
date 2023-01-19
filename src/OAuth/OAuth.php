<?php

declare(strict_types=1);

namespace App\OAuth;

use App\Auth\TokenFactory;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\UserRepository;
use App\Exceptions\UnauthorizedException;
use App\OAuth\Objects\TokenObject;
use Psr\Log\LoggerInterface;

class OAuth
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TokenFactory $token,
        private readonly UserRepository $users,
    ) {
    }

    /**
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    public function authorize(string $userName, string $password): TokenObject
    {
        $user = $this->getUser($userName);
        $this->verifyPassword($user, $password);

        $token = $this->token->encode([
            'sub' => $user->getId(),
        ]);

        $this->updateUser($user);

        $this->logger->info('User logged in.', [
            'id' => $userName,
        ]);

        return (new TokenObject())
            ->withAccessToken($token);
    }

    /**
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    private function getUser(string $userName): UserEntity
    {
        try {
            $user = $this->users->get($userName);
        } catch (RecordNotFoundException) {
            $this->logger->debug('Authentication failed.', [
                'reason' => 'user not found',
                'id' => $userName,
            ]);

            throw new UnauthorizedException('user not found');
        }

        return $user;
    }

    /**
     * @throws DatabaseException
     */
    private function updateUser(UserEntity $user): void
    {
        $user->setLoginAt(time());
        $this->users->update($user);
    }

    /**
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    private function verifyPassword(UserEntity $user, string $password): void
    {
        if (!password_verify($password, $user->getPassword())) {
            throw new UnauthorizedException('wrong password');
        }
    }
}
