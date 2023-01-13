<?php

declare(strict_types=1);

namespace App\Pages;

use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\PageRepository;
use App\Exceptions\PageNotFoundException;
use App\Pages\Objects\PageObject;
use League\CommonMark\CommonMarkConverter;
use RuntimeException;

class Pages
{
    public function __construct(private readonly CommonMarkConverter $md, private readonly PageRepository $pages)
    {
    }

    /**
     * @throws DatabaseException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function get(string $key): PageObject
    {
        try {
            $page = $this->pages->get($key);
            $html = $this->md->convert($page->getText());

            return (new PageObject())
                ->withEntity($page)
                ->withHTML((string)$html);
        } catch (RecordNotFoundException) {
            throw new PageNotFoundException();
        }
    }

    /**
     * @throws DatabaseException
     */
    public function put(string $id, string $text): void
    {
        try {
            $page = $this->pages->get($id);
            $page->setText($text);
            $page->setUpdated(time());

            $this->pages->update($page);
        } catch (RecordNotFoundException) {
            $page = new PageEntity();
            $page->setId($id);
            $page->setText($text);
            $page->setUpdated(time());

            $this->pages->add($page);
        }
    }
}
