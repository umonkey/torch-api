<?php

declare(strict_types=1);

namespace App\Pages\Tests;

use App\Core\AbstractTestCase;
use App\Database\Exceptions\DatabaseException;
use App\Database\Repositories\UserRepository;
use App\Exceptions\ConfigException;
use App\Pages\ListPages;
use League\CommonMark\Exception\AlreadyInitializedException;
use Psr\Container\ContainerExceptionInterface;

class ListPagesTests extends AbstractTestCase
{
    private readonly ListPages $handler;

    private readonly UserRepository $users;

    /**
     * @throws DatabaseException
     */
    public function testGetPages(): void
    {
        $this->fixture('002.yaml');

        $user = $this->users->get('phpunit');
        $pages = $this->handler->getPages($user);

        self::assertEquals([
            [
                'id' => 'foobar',
            ],
            [
                'id' => 'snafu',
            ],
        ], $pages);
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

        $this->handler = $this->container->get(ListPages::class);
        $this->users = $this->container->get(UserRepository::class);
    }
}
