<?php

declare(strict_types=1);

namespace App\Database\Entities;

use App\Database\Exceptions\DatabaseException;

class UserEntity extends AbstractEntity
{
    private const CREATED_AT_FIELD = 'created_at';
    private const EMAIL_FIELD = 'email';
    private const ID_FIELD = 'id';
    private const LOGIN_AT_FIELD = 'login_at';
    private const PASSWORD_FIELD = 'password';

    protected const PROP_TYPES = [
        self::CREATED_AT_FIELD => ['integer'],
        self::EMAIL_FIELD => ['string'],
        self::ID_FIELD => ['string'],
        self::LOGIN_AT_FIELD => ['integer', 'NULL'],
        self::PASSWORD_FIELD => ['string'],
    ];

    /**
     * @throws DatabaseException
     */
    public function getCreatedAt(): int
    {
        return parent::requireInt(self::CREATED_AT_FIELD);
    }

    /**
     * @throws DatabaseException
     */
    public function getEmail(): string
    {
        return parent::requireString(self::EMAIL_FIELD);
    }

    /**
     * @throws DatabaseException
     */
    public function getId(): string
    {
        return parent::requireString(self::ID_FIELD);
    }

    /**
     * @throws DatabaseException
     */
    public function getLoginAt(): int
    {
        return parent::requireInt(self::LOGIN_AT_FIELD);
    }

    /**
     * @throws DatabaseException
     */
    public function getPassword(): string
    {
        return parent::requireString(self::PASSWORD_FIELD);
    }

    public function setCreatedAt(int $value): void
    {
        parent::setInt(self::CREATED_AT_FIELD, $value);
    }

    public function setEmail(string $value): void
    {
        parent::setString(self::EMAIL_FIELD, $value);
    }

    public function setId(string $value): void
    {
        parent::setString(self::ID_FIELD, $value);
    }

    public function setLoginAt(int $value): void
    {
        parent::setInt(self::LOGIN_AT_FIELD, $value);
    }

    public function setPassword(string $value): void
    {
        parent::setString(self::PASSWORD_FIELD, $value);
    }

    /**
     * @return array<mixed>
     */
    protected static function getDefaults(): array
    {
        return [
            self::CREATED_AT_FIELD => time(),
        ];
    }
}
