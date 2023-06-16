<?php

declare(strict_types=1);

namespace App\Pages\Responders\Tests;

use App\Core\AbstractTestCase;
use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadResponseException;
use App\Exceptions\ConfigException;
use App\Pages\Objects\PageObject;
use App\Pages\Responders\PageResponder;
use InvalidArgumentException;
use League\CommonMark\Exception\AlreadyInitializedException;
use Psr\Container\ContainerExceptionInterface;

class PageResponderTests extends AbstractTestCase
{
    private PageResponder $responder;

    /**
     * @throws BadResponseException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     */
    public function testRender(): void
    {
        $entity = new PageEntity();
        $entity->setId('foobar');
        $entity->setText('some text');
        $entity->setCreated(1);
        $entity->setUpdated(1);

        $page = (new PageObject())
            ->withEntity($entity);

        $res = $this->responder->respond($page);

        self::assertEquals('application/json; charset=utf-8', $res->getHeaderLine('content-type'));
        self::assertEquals('{"page":{"id":"foobar","created":1,"updated":1,"source":"some text","html":null}}', (string)$res->getBody());
    }

    /**
     * @throws AlreadyInitializedException
     * @throws ConfigException
     * @throws ContainerExceptionInterface
     * @throws DatabaseException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = $this->container->get(PageResponder::class);
    }
}
