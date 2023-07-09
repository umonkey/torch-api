<?php

declare(strict_types=1);

namespace App\OAuth\Tests;

use App\Core\AbstractTestCase;
use App\Database\Exceptions\DatabaseException;
use App\Database\Repositories\UserRepository;
use App\Exceptions\UnauthorizedException;
use App\OAuth\OAuth;

class OAuthTests extends AbstractTestCase
{
    private readonly OAuth $oauth;

    private readonly UserRepository $users;

    /**
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    public function testUserNotFound(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->oauth->authorize('foobar', 'foobar');
    }

    /**
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    public function testBadPassword(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->fixture('001.yaml');

        $this->oauth->authorize('foobar', 'secret!');
    }

    /**
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    public function testGoodPassword(): void
    {
        $this->fixture('001.yaml');

        $user = $this->users->get('foobar');
        self::assertEquals(0, $user->getLoginAt());

        $this->oauth->authorize('foobar', 'secret');

        $user = $this->users->get('foobar');
        self::assertNotEquals(0, $user->getLoginAt());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->oauth = $this->container->get(OAuth::class);
        $this->users = $this->container->get(UserRepository::class);
    }
}
