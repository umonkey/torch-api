<?php

declare(strict_types=1);

namespace App\Database\Repositories\Tests;

use App\Core\AbstractTestCase;
use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\DuplicateRecordException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\PageRepository;
use App\Exceptions\ConfigException;
use Psr\Container\ContainerExceptionInterface;

class PageRepositoryTests extends AbstractTestCase
{
    private PageRepository $repo;

    /**
     * @throws DatabaseException
     */
    public function testPageNotFound(): void
    {
        $this->expectException(RecordNotFoundException::class);

        $this->repo->get('foobar');
    }

    /**
     * @throws DatabaseException
     */
    public function testDuplicatePage(): void
    {
        $this->expectException(DuplicateRecordException::class);

        $page = new PageEntity();
        $page->setId('foobar');
        $page->setText('hello');

        $this->repo->add($page);
        $this->repo->add($page);
    }

    /**
     * @throws DatabaseException
     */
    public function testPageFound(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new PageEntity();
        $page->setId('foobar');
        $page->setText('hello');

        $this->repo->add($page);

        $this->repo->get('foobar');
    }

    /**
     * @throws ConfigException
     * @throws ContainerExceptionInterface
     * @throws DatabaseException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->container->get(PageRepository::class);
    }
}
