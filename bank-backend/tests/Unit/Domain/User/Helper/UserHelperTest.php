<?php


namespace Tests\Unit\Domain\User\Helper;

/**
 * User helper is jsut to help other tests to create some test using User model
 */
final class UserHelperTest{

    public static function getDefaultUserData($admin = false, $username="renan", $email = "mail@mail.com") : array
    {
        return [
            'username' => $username,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => 'anaaa',
            'remember_token' => 'token',
            'is_admin' => $admin
        ];
    }

}