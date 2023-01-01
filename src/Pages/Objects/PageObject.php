<?php

declare(strict_types=1);

namespace App\Pages\Objects;

use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
use InvalidArgumentException;

class PageObject
{
    private ?string $html = null;

    private ?PageEntity $page = null;

    public function __construct()
    {
    }

    /**
     * @return array<mixed>
     * @throws DatabaseException
     * @throws InvalidArgumentException
     */
    public function serialize(): array
    {
        $page = $this->page ?? throw new InvalidArgumentException('page not set');

        return [
            'id' => $page->getId(),
            'created' => $page->getCreated(),
            'updated' => $page->getUpdated(),
            'source' => $page->getText(),
            'html' => $this->html,
        ];
    }

    public function withEntity(PageEntity $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function withHTML(string $html): self
    {
        $this->html = $html;
        return $this;
    }
}
