<?php

declare(strict_types=1);

namespace App\Database\Entities\Tests;

use App\Core\AbstractTestCase;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;

class UserEntityTests extends AbstractTestCase
{
    /**
     * @throws DatabaseException
     */
    public function testGetCreated(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new UserEntity();
        $page->setCreatedAt(time());
        $page->getCreatedAt();
    }

    /**
     * @throws DatabaseException
     */
    public function testGetCreatedUnset(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new UserEntity();
        $page->getCreatedAt();
    }

    /**
     * Should not fail, just return empty value.
     *
     * @throws DatabaseException
     */
    public function testLoginAt(): void
    {
        $this->expectException(DatabaseException::class);

        $page = new UserEntity();
        $page->getLoginAt();
    }

    /**
     * @throws DatabaseException
     */
    public function testSetUpdated(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new UserEntity();
        $page->setLoginAt(123);
        $page->getLoginAt();
    }

    /**
     * @throws DatabaseException
     */
    public function testGetPassword(): void
    {
        $this->expectException(DatabaseException::class);

        $page = new UserEntity();
        $page->getPassword();
    }
}
