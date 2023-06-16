<?php

declare(strict_types=1);

namespace App\Database\Repositories;

use App\Database\DatabaseInterface;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;

class UserRepository
{
    public function __construct(private readonly DatabaseInterface $db)
    {
    }

    /**
     * @throws DatabaseException
     */
    public function add(UserEntity $user): void
    {
        $row = $user->serialize();
        $this->db->addUser($row);
    }

    /**
     * @throws DatabaseException
     */
    public function get(string $id): UserEntity
    {
        $row = $this->db->getUser($id);
        return new UserEntity($row);
    }

    /**
     * @throws DatabaseException
     */
    public function update(UserEntity $user): void
    {
        $this->db->updateUser($user->toArray());
    }
}
