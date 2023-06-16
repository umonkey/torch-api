<?php

declare(strict_types=1);

namespace App\Pages\Tests;

use App\Core\AbstractTestCase;
use App\Database\Entities\PageEntity;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Repositories\PageRepository;
use App\Exceptions\ConfigException;
use App\Exceptions\PageNotFoundException;
use App\Pages\Pages;
use InvalidArgumentException;
use League\CommonMark\Exception\AlreadyInitializedException;
use League\CommonMark\Exception\CommonMarkException;
use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class PagesTests extends AbstractTestCase
{
    private Pages $pages;

    private PageRepository $repo;

    /**
     * @throws CommonMarkException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function testRenderPage(): void
    {
        $page = new PageEntity();
        $page->setId('foobar');
        $page->setText('# hello');

        $this->repo->add($page);

        $user = new UserEntity();
        $user->setId('phpunit');

        $res = $this->pages->get($page->getId(), $user);
        $props = $res->serialize();

        self::assertEquals("<h1>hello</h1>\n", $props['html']);
    }

    /**
     * @throws CommonMarkException
     * @throws DatabaseException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function testNotFound(): void
    {
        $this->expectException(PageNotFoundException::class);

        $user = new UserEntity();
        $user->setId('phpunit');

        $this->pages->get('foobar', $user);
    }

    /**
     * @throws CommonMarkException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function testUpdateNotFound(): void
    {
        $user = new UserEntity();
        $user->setId('phpunit');

        $this->pages->put('foobar', 'some text', $user);

        $page = $this->pages->get('foobar', $user)->serialize();

        self::assertEquals('foobar', $page['id']);
        self::assertEquals('some text', $page['source']);
    }

    /**
     * @throws CommonMarkException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function testUpdate(): void
    {
        $user = new UserEntity();
        $user->setId('phpunit');

        $this->pages->put('foobar', 'some text', $user);
        $this->pages->put('foobar', 'more text', $user);

        $page = $this->pages->get('foobar', $user)->serialize();

        self::assertEquals('foobar', $page['id']);
        self::assertEquals('more text', $page['source']);
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

        $this->pages = $this->container->get(Pages::class);
        $this->repo = $this->container->get(PageRepository::class);
    }
}
