<?php

declare(strict_types=1);

namespace App\Auth\Tests;

use App\Auth\Exceptions\AuthorizationMissingException;
use App\Auth\Exceptions\BadTokenFormatException;
use App\Auth\TokenAuthClient;
use App\Auth\TokenFactory;
use App\Core\AbstractTestCase;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use InvalidArgumentException;
use Slim\Psr7\Factory\ServerRequestFactory;

class TokenAuthClientTests extends AbstractTestCase
{
    private readonly TokenFactory $factory;

    private readonly TokenAuthClient $client;

    /**
     * @throws DatabaseException
     * @throws UnauthorizedException
     */
    public function testMissingHeader(): void
    {
        $this->expectException(AuthorizationMissingException::class);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', []);

        $this->client->authenticate($request);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function testBadTokenType(): void
    {
        $this->expectException(BadTokenFormatException::class);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', [])
            ->withHeader('authorization', 'foobar foo.bar.baz');

        $this->client->authenticate($request);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function testGood(): void
    {
        $this->fixture('001.yaml');

        $token = $this->factory->encode([
            'sub' => 'phpunit',
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', [])
            ->withHeader('authorization', 'bearer ' . $token);

        $user = $this->client->authenticate($request);

        self::assertEquals('phpunit', $user->getId());
        self::assertEquals('test@example.com', $user->getEmail());
    }

    /**
     * Must have two words: bearer, and the token.
     *
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function testFewTokenParts(): void
    {
        $this->expectException(BadTokenFormatException::class);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', [])
            ->withHeader('authorization', 'foo.bar.baz');

        $this->client->authenticate($request);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function testUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->fixture('001.yaml');

        $token = $this->factory->encode([
            'sub' => 'other',
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', [])
            ->withHeader('authorization', 'bearer ' . $token);

        $this->client->authenticate($request);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->container->get(TokenFactory::class);
        $this->client = $this->container->get(TokenAuthClient::class);
    }
}
