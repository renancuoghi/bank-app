<?php

namespace Tests\Unit\Domain\User\Repository;

use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Repository\UserRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Domain\User\Helper\UserHelperTest;

class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private UserRepositoryInterface $userRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(UserRepository::class);
    }

    public function test_insert_user()
    {
        $user = $this->userRepository->create(UserHelperTest::getDefaultUserData(false));

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('renan', $user->username);
        $this->assertGreaterThan(0, $user->id);
        $this->assertFalse($user->isAdmin());
    }

    public function test_insert_admin()
    {
        $user = $this->userRepository->create(UserHelperTest::getDefaultUserData(true));

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('renan', $user->username);
        $this->assertGreaterThan(0, $user->id);
        $this->assertTrue($user->isAdmin());
    }


    public function test_unique_username()
    {
        $this->expectException(QueryException::class);
        $user = $this->userRepository->create(UserHelperTest::getDefaultUserData(false));
        // testing with different email (to garantee uniqueness constraint for username)
        $data = UserHelperTest::getDefaultUserData(false);
        $data['email'] = 'different@example.com';
        $user2 = $this->userRepository->create($data);
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_unique_email()
    {
        $this->expectException(QueryException::class);
        $user = $this->userRepository->create(UserHelperTest::getDefaultUserData(false));
        // testing with different username (to garantee uniqueness constraint for email)
        $data = UserHelperTest::getDefaultUserData(false);
        $data['username'] = 'myuser2';
        $user2 = $this->userRepository->create($data);
        $this->assertInstanceOf(User::class, $user);
    }


    public function test_update_user()
    {
        $data = UserHelperTest::getDefaultUserData(false);
        $user = $this->userRepository->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('renan', $user->username);
        $this->assertGreaterThan(0, $user->id);
        $this->assertFalse($user->isAdmin());

        $user->is_admin = true;
        $userData =  $user->toArray();
        $userData["password"] = 'mypassword';
        $updateUser = $this->userRepository->update($user->id, $userData);
        $this->assertTrue($updateUser->isAdmin());
    }



    
}
