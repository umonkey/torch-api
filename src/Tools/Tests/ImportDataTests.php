<?php

declare(strict_types=1);

namespace App\Tools\Tests;

use App\Core\AbstractTestCase;
use App\Database\Entities\PageEntity;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\PageRepository;
use App\Database\Repositories\UserRepository;
use App\Tools\ImportData;
use JsonException;

class ImportDataTests extends AbstractTestCase
{
    private readonly ImportData $importer;

    private readonly PageRepository $pages;

    private readonly UserRepository $users;

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testImport(): void
    {
        try {
            $this->pages->get('test');
            self::fail('test page already exists');
        } catch (RecordNotFoundException) {
            // OK!
        }

        $this->importer->import(__DIR__ . '/fixtures/archive.zip');

        $page = $this->pages->get('test');
        self::assertEquals('It Works!', $page->getText());
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testUpdate(): void
    {
        $this->pages->add(new PageEntity([
            'id' => 'test',
            'text' => 'foobar',
        ]));

        $this->users->add(new UserEntity([
            'id' => 'test',
            'email' => 'test@example.com',
            'password' => 'foobar',
        ]));

        $this->importer->import(__DIR__ . '/fixtures/archive.zip');

        $page = $this->pages->get('test');
        self::assertEquals('It Works!', $page->getText());

        $user = $this->users->get('test');
        self::assertEquals('secret', $user->getPassword());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->importer = $this->container->get(ImportData::class);
        $this->pages = $this->container->get(PageRepository::class);
        $this->users = $this->container->get(UserRepository::class);
    }
}
