<?php

declare(strict_types=1);

namespace App\Pages;

use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\PageRepository;
use App\Exceptions\PageNotFoundException;

class Pages
{
    public function __construct(private readonly PageRepository $pages)
    {
    }

    /**
     * @throws DatabaseException
     * @throws PageNotFoundException
     */
    public function get(string $key): PageEntity
    {
        try {
            return $this->pages->get($key);
        } catch (RecordNotFoundException) {
            throw new PageNotFoundException();
        }
    }
}
