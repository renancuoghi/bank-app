<?php

namespace App\Domain\User\Service;

use App\Domain\User\Model\User;

interface UserServiceInterface
{
    public function create(array $data): ?User;

    public function createToken(string $username): string;

    public function getByUsername(string $username): User;
}
