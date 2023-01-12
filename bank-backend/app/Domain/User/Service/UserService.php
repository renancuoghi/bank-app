<?php

namespace App\Domain\User\Service;

use App\Domain\Balance\Balance\Exception\InvalidUserException;
use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;


    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @throws InvalidUserException
     */
    public function create(array $data): ?User
    {
        return $this->userRepository->create($data);
    }

    /**
     * Create a new token for a given user
     *
     * @param string username
     *
     * @return string
     */
    public function createToken(string $username): string
    {
        $user = User::where('username', $username)->first();
        if ($user) {
            return $user->createToken("API TOKEN")->plainTextToken;
        }
        throw new InvalidUserException("User invalid or not exist");
    }

    public function getByUsername(string $username): User
    {
        return $this->userRepository->getByUsername($username);
    }
}
