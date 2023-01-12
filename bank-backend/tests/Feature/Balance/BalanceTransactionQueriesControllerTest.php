<?php

namespace Tests\Feature\Balance;


use App\Domain\User\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\Unit\Domain\Balance\BaseBalanceTest;

class BalanceTransactionQueriesControllerTest extends BaseBalanceTest{

    use DatabaseTransactions;

    public function test_last_transactions()
    {

        $user = User::factory()->create();

        $route  = $this->createPaginatorRequest('balance-transaction-last');

        $response = $this->actingAs($user)
                        ->get($route);


        $this->validateReponsePaginations($response);
    }

    public function test_last_transactions_admin_denied()
    {

        $admin = User::where("username", "admin")->first();

        $route  = $this->createPaginatorRequest('balance-transaction-last');

        $response = $this->actingAs($admin)
                        ->get($route);


        $response->assertStatus(403);
    }

    public function test_pending_transactions()
    {

        $admin = User::where("username", "admin")->first();

        $route  = $this->createPaginatorRequest('balance-transaction-pending',null);

        $response = $this->actingAs($admin)
                        ->get($route);
        // $response->dd();
        $this->validateReponsePaginations($response);

    }

    public function test_pending_transactions_user_denied()
    {

        $user = User::factory()->create();

        $route  = $this->createPaginatorRequest('balance-transaction-pending',null);

        $response = $this->actingAs($user)
                        ->get($route);

        $response->assertStatus(403);

    }


    public function test_credit_transactions()
    {

        $user = User::factory()->create();

        $route  = $this->createPaginatorRequest('balance-transaction-credit');

        $response = $this->actingAs($user)
                        ->get($route);


        $this->validateReponsePaginations($response);
    }

    public function test_credit_transactions_admin_denied()
    {

        $admin = User::where("username", "admin")->first();

        $route  = $this->createPaginatorRequest('balance-transaction-credit');

        $response = $this->actingAs($admin)
                        ->get($route);

        $response->assertStatus(403);
    }

    public function test_debit_transactions()
    {

        $user = User::factory()->create();

        $route  = $this->createPaginatorRequest('balance-transaction-debit');

        $response = $this->actingAs($user)
                        ->get($route);


        $this->validateReponsePaginations($response);
    }

    public function test_debit_transactions_admin_denied()
    {

        $admin = User::where("username", "admin")->first();

        $route  = $this->createPaginatorRequest('balance-transaction-debit');

        $response = $this->actingAs($admin)
                        ->get($route);


        $response->assertStatus(403);
    }


    public function test_total_amount_by_transaction_type()
    {

        $user = User::factory()->create();

        $route  = $this->createPaginatorRequest('balance-transaction-total-transaction-type');

        $response = $this->actingAs($user)
                        ->get($route);


        $response->assertStatus(200)
                        ->assertJson([
                           'status' => true,
                           'data' => [
                               'incoming' => 0,
                               'expenses' => 0,
                           ]
       ]);
    }

    public function test_total_amount_by_transaction_type_admin_denied()
    {

        $admin = User::where("username", "admin")->first();

        $route  = $this->createPaginatorRequest('balance-transaction-total-transaction-type');

        $response = $this->actingAs($admin)
                        ->get($route);


        $response->assertStatus(403);
    }


    private function createPaginatorRequest(string $route, $date = null, $page = 1, $page_size = 10) : string
    {
        if(isset($date) === false){
            $date = date('Y-m-d');
        }

        return route($route) . "?date={$date}&page={$page}&page_size={$page_size}";

    }

    private function validateReponsePaginations($response){
        $response->assertStatus(200)
                 ->assertJson([
                    'status' => true,
                ])
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'total_pages',
                        'page_size',
                        'total',
                        'items'
                    ]
                ]);
    }

}
