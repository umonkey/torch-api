<?php

declare(strict_types=1);

namespace App\Database\Repositories\Tests;

use App\Core\AbstractTestCase;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\DuplicateRecordException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\UserRepository;
use App\Exceptions\ConfigException;
use Psr\Container\ContainerExceptionInterface;

class UserRepositoryTests extends AbstractTestCase
{
    private UserRepository $repo;

    /**
     * @throws DatabaseException
     */
    public function testUserNotFound(): void
    {
        $this->expectException(RecordNotFoundException::class);

        $this->repo->get('foobar');
    }

    /**
     * @throws DatabaseException
     */
    public function testDuplicateUser(): void
    {
        $this->expectException(DuplicateRecordException::class);

        $user = new UserEntity();
        $user->setId('foobar');
        $user->setEmail('foo@bar.com');
        $user->setPassword('secret');

        $this->repo->add($user);
        $this->repo->add($user);
    }

    /**
     * @throws DatabaseException
     */
    public function testUserFound(): void
    {
        $this->expectNotToPerformAssertions();

        $user = new UserEntity();
        $user->setId('foobar');
        $user->setEmail('foo@bar.com');
        $user->setLoginAt(time());
        $user->setPassword('secret');

        $this->repo->add($user);

        $this->repo->get('foobar');
    }

    /**
     * @throws DatabaseException
     */
    public function testUpdateUser(): void
    {
        $user = new UserEntity();
        $user->setId('foobar');
        $user->setEmail('foo@bar.com');
        $user->setPassword('secret');

        $this->repo->add($user);

        $user->setEmail('bar@foo.com');

        $this->repo->update($user);

        $updated = $this->repo->get($user->getId());
        self::assertEquals('bar@foo.com', $updated->getEmail());
    }

    /**
     * @throws ConfigException
     * @throws ContainerExceptionInterface
     * @throws DatabaseException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->container->get(UserRepository::class);
    }
}
