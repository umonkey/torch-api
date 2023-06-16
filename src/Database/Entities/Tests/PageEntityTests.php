<?php

declare(strict_types=1);

namespace App\Database\Entities\Tests;

use App\Core\AbstractTestCase;
use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;

class PageEntityTests extends AbstractTestCase
{
    /**
     * @throws DatabaseException
     */
    public function testGetCreated(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new PageEntity();
        $page->setCreated(time());
        $page->getCreated();
    }

    /**
     * @throws DatabaseException
     */
    public function testGetCreatedUnset(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new PageEntity();
        $page->getCreated();
    }

    /**
     * Should not fail because of defaults.
     *
     * @throws DatabaseException
     */
    public function testUpdated(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new PageEntity();
        $page->getUpdated();
    }

    /**
     * @throws DatabaseException
     */
    public function testSetUpdated(): void
    {
        $this->expectNotToPerformAssertions();

        $page = new PageEntity();
        $page->setUpdated(time());
        $page->getUpdated();
    }
}
