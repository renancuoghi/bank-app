<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Model\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user
     *
     * @param array<mixed> $data
     */
    public function create(array $data): ?User
    {
        $data["password"] = Hash::make($data["password"]);
        return User::create($data);
    }
    /**
     * Updates an existing user
     *
     * @param int $id
     * @param array<mixed> $data
     */
    public function update(int $id, array $data): ?User
    {
        if (User::whereId($id)->update($data)) {
            return $this->getById($id);
        }
        return null;
    }
    /**
     * Get user by id
     *
     * @param int $id
     */
    public function getById(int $id): ?User
    {
        return User::find($id);
    }

    public function getByUsername(string $username): User
    {
        return User::where("username", $username)->first();
    }
}
