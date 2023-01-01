<?php

declare(strict_types=1);

namespace App\Database\Entities;

use App\Database\Exceptions\DatabaseException;

class PageEntity extends AbstractEntity
{
    protected const PROP_TYPES = [
        'created' => ['integer'],
        'id' => ['string'],
        'text' => ['string'],
        'updated' => ['integer'],
    ];

    /**
     * @throws DatabaseException
     */
    public function getCreated(): int
    {
        return parent::requireInt('created');
    }

    /**
     * @throws DatabaseException
     */
    public function getId(): string
    {
        return parent::requireString('id');
    }

    /**
     * @throws DatabaseException
     */
    public function getText(): string
    {
        return parent::requireString('text');
    }

    /**
     * @throws DatabaseException
     */
    public function getUpdated(): int
    {
        return parent::requireInt('updated');
    }

    public function setCreated(int $value): void
    {
        parent::setInt('created', $value);
    }

    public function setId(string $value): void
    {
        parent::setString('id', $value);
    }

    public function setText(string $value): void
    {
        parent::setString('text', $value);
    }

    public function setUpdated(int $value): void
    {
        parent::setInt('updated', $value);
    }

    /**
     * @return array<mixed>
     */
    protected static function getDefaults(): array
    {
        return [
            'created' => time(),
            'updated' => time(),
        ];
    }
}
