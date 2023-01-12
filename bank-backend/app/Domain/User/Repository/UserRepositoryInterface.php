<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\Shared\Interface\RepositoryInterface;
use App\Domain\User\Model\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getByUsername(string $username): User;
}
