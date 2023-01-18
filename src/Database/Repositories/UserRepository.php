<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\DatabaseInterface;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;

class UserRepository
{
    private const TABLE_NAME = 'users';

    public function __construct(private readonly DatabaseInterface $db)
    {
    }

    /**
     * @throws DatabaseException
     */
    public function add(UserEntity $user): void
    {
        $row = $user->serialize();
        $this->db->add(self::TABLE_NAME, $row);
    }

    /**
     * @throws DatabaseException
     */
    public function get(string $id): UserEntity
    {
        $row = $this->db->get(self::TABLE_NAME, [
            'id = :id' => [
                ':id' => $id,
            ],
        ]);

        return new UserEntity($row);
    }

    /**
     * @throws DatabaseException
     */
    public function update(UserEntity $user): void
    {
        $this->db->update(self::TABLE_NAME, [
            'id = :id' => [
                ':id' => $user->getId(),
            ],
        ], [
            'created_at' => $user->getCreatedAt(),
            'email' => $user->getEmail(),
            'login_at' => $user->getLoginAt(),
            'password' => $user->getPassword(),
        ]);
    }
}
