<?php

declare(strict_types=1);

namespace App\Pages\Tests;

use App\Core\AbstractTestCase;
use App\Core\Config;
use App\Core\Logging\ConsoleLogger;
use App\Database\Drivers\MemoryDriver;
use App\Database\Entities\PageEntity;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Repositories\PageRepository;
use App\Exceptions\ConfigException;
use App\Exceptions\PageNotFoundException;
use App\Pages\Markdown;
use App\Pages\Pages;
use InvalidArgumentException;
use RuntimeException;

class PagesTests extends AbstractTestCase
{
    private Pages $pages;

    private PageRepository $repo;

    /**
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
     * @throws ConfigException
     * @throws DatabaseException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $config = new Config();
        $db = new MemoryDriver($config);
        $md = new Markdown();
        $logger = new ConsoleLogger($config);

        $this->repo = new PageRepository($db);

        $this->pages = new Pages(
            md: $md,
            logger: $logger,
            pages: $this->repo,
        );
    }
}
