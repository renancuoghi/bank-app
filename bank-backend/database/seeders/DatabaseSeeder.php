<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\User\Model\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Tests\Unit\Helper\File\ImageRepo;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // creating some users
        User::factory(3)->create();
        // create a admin user
        $admin = User::where('username' , 'admin')->first();
        if(isset($admin) === false){
            User::factory()->create([
                'username' => 'admin',
                'email' => 'admin@bnbbank.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'is_admin' => true,
            ]);
        }
        if(User::where('username' , 'usertest')->exists() === false){
            $user = User::factory()->create([
                'username' => 'usertest',
                'email' => 'user@user.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'is_admin' => false,
            ]);
            // creating balance
            $balance = Balance::create([
                'user_id' => $user->id,
                'total' => 1000
            ]);
            // accepted transaction
            for($i=0;$i<5;$i++){
                BalanceTransaction::create([
                    'user_id' => $user->id,
                    'balance_id' => $balance->id,
                    'amount' => 200,
                    'transaction_type' => TransactionType::CREDIT->value,
                    'description' => fake()->name(),
                    'status' =>  TransactionStatus::ACCEPTED->value,
                    'path_image' => "https://geekflare.com/wp-content/uploads/2019/11/h3-test-1200x385.jpg",
                    'created_at' => Carbon::now()
                ]);
            }
            // rejected
            for($i=0;$i<2;$i++){
                BalanceTransaction::create([
                    'user_id' => $user->id,
                    'balance_id' => $balance->id,
                    'amount' => 200,
                    'transaction_type' => TransactionType::CREDIT->value,
                    'description' => fake()->name(),
                    'status' =>  TransactionStatus::REJECTED->value,
                    'path_image' => "https://geekflare.com/wp-content/uploads/2019/11/h3-test-1200x385.jpg",
                    'created_at' => Carbon::now()
                ]);
            }

            // pending
            for($i=0;$i<3;$i++){
                BalanceTransaction::create([
                    'user_id' => $user->id,
                    'balance_id' => $balance->id,
                    'amount' => 200,
                    'transaction_type' => TransactionType::CREDIT->value,
                    'description' => fake()->name(),
                    'status' =>  TransactionStatus::PENDING->value,
                    'path_image' => "https://geekflare.com/wp-content/uploads/2019/11/h3-test-1200x385.jpg",
                    'created_at' => Carbon::now()
                ]);
            }

        }




    }
}
