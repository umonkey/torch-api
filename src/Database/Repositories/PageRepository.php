<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\DatabaseInterface;
use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;

class PageRepository
{
    private const TABLE_NAME = 'pages';

    public function __construct(private readonly DatabaseInterface $db)
    {
    }

    /**
     * @throws DatabaseException
     */
    public function get(string $id): PageEntity
    {
        $row = $this->db->get(self::TABLE_NAME, [
            'id = :id' => [
                ':id' => $id,
            ],
        ]);

        return new PageEntity($row);
    }

    /**
     * @throws DatabaseException
     */
    public function add(PageEntity $page): void
    {
        $row = $page->serialize();
        $this->db->add(self::TABLE_NAME, $row);
    }
}
