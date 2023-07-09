<?php

declare(strict_types=1);

namespace App\Index\Actions\Tests;

use App\Core\AbstractTestCase;
use App\Index\Actions\IndexAction;
use InvalidArgumentException;
use RuntimeException;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class IndexActionTests extends AbstractTestCase
{
    private readonly IndexAction $action;

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testIndex(): void
    {
        $req = (new ServerRequestFactory())
            ->createServerRequest('POST', '/', []);

        $res = $this->action->__invoke(
            request: $req,
            response: new Response(),
        );

        self::assertEquals(200, $res->getStatusCode());
        self::assertEquals('It Works!', (string)$res->getBody());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = $this->container->get(IndexAction::class);
    }
}
