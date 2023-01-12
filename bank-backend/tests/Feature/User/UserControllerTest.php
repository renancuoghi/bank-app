<?php

namespace Tests\Feature\User;

use App\Domain\User\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Domain\User\Helper\UserHelperTest;

class UserControllerTest extends TestCase{

    use DatabaseTransactions;

    public function test_login()
    {

        $adminDefault = ['username' => 'admin', 'password' => 'password'];
        $response = $this->postJson(route('user-login'), $adminDefault);
        $response->assertStatus(200)
                 ->assertJson(['status' => true])
                 ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'token'
                    ]
                ]);
    }


    public function test_login_invalid_request()
    {        
        $response = $this->postJson(route('user-login'), []);
        $response->assertStatus(422)
                 ->assertJson(['status' => false])
                 ->assertJsonStructure([
                    'status',
                    'error',
                    'data' => [
                        'username',
                        'password'
                    ]
                ]);

    }

    public function test_login_invalid_login()
    {        
        $request = ['username' => 'admin2', 'password' => 'password2'];
        $response = $this->postJson(route('user-login'), $request);
        $response->assertStatus(401)
                 ->assertJson(['status' => false])
                 ->assertJsonStructure([
                    'status',
                    'error',
                    'data'
                ]);

    }

    public function test_create_new_user(){
        $this->assertFalse(User::where('username', 'user2')->exists());
        $request = ['username' => 'user2', 'email' => 'myuser@user.com', 'password' => 'asd@jq3ed1329SA'];
        $response = $this->postJson(route('user-register'), $request);
        $response->assertStatus(201)
                 ->assertJson([
                        'status' => true,
                        'data' => [
                            'username' => 'user2',
                            'email' =>'myuser@user.com',
                        ]
                    ]
                );

        $newUser = User::where('username', 'user2')->first();                
        $this->assertNotNull($newUser);
        $this->assertGreaterThan(0, $newUser->id);
        $this->assertFalse($newUser->isAdmin());
    }

    public function test_create_user_weak_password(){
        $request = ['username' => 'user2', 'email' => 'myuser@user.com', 'password' => 'testeteste'];
        $response = $this->postJson(route('user-register'), $request);
        $response->assertStatus(422)
        ->assertJson(['status' => false])
        ->assertJsonStructure([
           'status',
           'error',
           'data'
       ]);
    }


    public function test_create_user_unique_user(){
        $username = 'user2';
        $userData = UserHelperTest::getDefaultUserData(false, $username);
        $newUser = User::create($userData);
        $this->assertTrue(User::where('username', $username)->exists());
        
        $request = ['username' => $username, 'email' => 'myuser@user.com', 'password' => 'asd@jq3ed1329SA'];
        $response = $this->postJson(route('user-register'), $request);
        $response->assertStatus(422)
                 ->assertJson([
                        'status' => false,        
                    ]
                );        
    }

    public function test_create_user_unique_email(){
        $username = 'user2';
        $email = 'myuser@user.com';
        $userData = UserHelperTest::getDefaultUserData(false, $username, $email);
        $newUser = User::create($userData);
        $this->assertTrue(User::where('username', $username)->exists());
        
        $request = ['username' => $username, 'email' => $email, 'password' => 'asd@jq3ed1329SA'];
        $response = $this->postJson(route('user-register'), $request);
        $response->assertStatus(422)
                 ->assertJson([
                        'status' => false,        
                    ]
                );        
    }

    public function test_create_user_empty_request(){
                
        $response = $this->postJson(route('user-register'));
        $response->assertStatus(422)
                 ->assertJson([
                        'status' => false,        
                    ]
                );        
    }


    public function test_get_balance()
    {

        $userData = UserHelperTest::getDefaultUserData(false, 'aaaaaa', 'bbbb@bbb.com');
        $newUser = User::create($userData);

        $response = $this->actingAs($newUser)
                            ->get(route('user-balance'));
                            
        $response->assertStatus(200)
                 ->assertJson(['status' => true])
                 ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'id',
                        'total'
                    ]
                ]);
    }

    public function test_get_balance_admin_denied()
    {

        $admin = User::where("username", "admin")->first();
        
        $response = $this->actingAs($admin)
                            ->get(route('user-balance'));
                            
        $response->assertStatus(403);
    }

    public function test_get_balance_without_authentication()
    {
                
        $response = $this->get(route('user-balance'));
                            
        $response->assertStatus(401);
    }
}