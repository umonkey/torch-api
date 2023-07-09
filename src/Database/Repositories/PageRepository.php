<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\DatabaseInterface;
use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
use Generator;

class PageRepository
{
    public function __construct(private readonly DatabaseInterface $db)
    {
    }

    /**
     * @throws DatabaseException
     */
    public function add(PageEntity $page): void
    {
        $row = $page->serialize();
        $this->db->addPage($row);
    }

    /**
     * @throws DatabaseException
     */
    public function get(string $id): PageEntity
    {
        $row = $this->db->getPage($id);
        return new PageEntity($row);
    }

    /**
     * @throws DatabaseException
     */
    public function update(PageEntity $page): void
    {
        $this->db->updatePage($page->toArray());
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function delete(string $id): void
    {
        $this->db->deletePage($id);
    }

    /**
     * @return PageEntity[]|Generator
     * @throws DatabaseException
     */
    public function iter(): Generator
    {
        foreach ($this->db->findPages() as $row) {
            yield new PageEntity($row);
        }
    }
}
