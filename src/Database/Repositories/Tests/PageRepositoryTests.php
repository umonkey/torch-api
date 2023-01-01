<?php

declare(strict_types=1);

namespace App\Database\Repositories\Tests;

use App\Core\AbstractTestCase;
use App\Core\Config;
use App\Database\DatabaseInterface;
use App\Database\Drivers\MemoryDriver;
use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\DuplicateRecordException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\PageRepository;
use App\Exceptions\ConfigException;

class PageRepositoryTests extends AbstractTestCase
{
    private DatabaseInterface $db;

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
     * @throws DatabaseException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $config = new Config();
        $this->db = new MemoryDriver($config);
        $this->repo = new PageRepository($this->db);
    }
}
