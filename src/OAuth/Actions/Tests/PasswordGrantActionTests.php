<?php

declare(strict_types=1);

namespace App\OAuth\Actions\Tests;

use App\Core\AbstractTestCase;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use App\OAuth\Actions\PasswordGrantAction;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class PasswordGrantActionTests extends AbstractTestCase
{
    private readonly PasswordGrantAction $action;

    /**
     * @throws BadRequestException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     * @throws UnauthorizedException
     */
    public function testSuccess(): void
    {
        $this->fixture('001.yaml');

        $req = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', [])
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withParsedBody([
                'grant_type' => 'password',
                'username' => 'phpunit',
                'password' => 'secret',
            ]);

        $res = $this->action->__invoke($req, new Response());

        self::assertEquals(200, $res->getStatusCode());
        self::assertEquals('application/json', $res->getHeaderLine('content-type'));
    }

    /**
     * @throws BadRequestException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     * @throws UnauthorizedException
     */
    public function testWrongGrant(): void
    {
        $this->expectException(BadRequestException::class);

        $this->fixture('001.yaml');

        $req = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', [])
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withParsedBody([
                'grant_type' => 'client',
                'username' => 'phpunit',
                'password' => 'secret',
            ]);

        $this->action->__invoke($req, new Response());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = $this->container->get(PasswordGrantAction::class);
    }
}
