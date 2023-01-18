<?php

declare(strict_types=1);

namespace App\Auth;

use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\UserRepository;
use App\Exceptions\UserNotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class DefaultAuthClient implements AuthInterface
{
    public function __construct(private readonly LoggerInterface $logger, private readonly UserRepository $users)
    {
    }

    /**
     * @throws DatabaseException
     * @throws UserNotFoundException
     */
    public function authenticate(ServerRequestInterface $request): UserEntity
    {
        $userId = $this->getUserId();

        try {
            return $this->users->get($userId);
        } catch (RecordNotFoundException) {
            $this->logger->debug('User not found, cannot authenticate.', [
                'id' => $userId,
            ]);

            throw new UserNotFoundException();
        }
    }

    private function getUserId(): string
    {
        return 'umonkey';
    }
}
