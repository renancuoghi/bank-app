<?php

namespace Tests\Feature\Balance;

use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\User\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Unit\Domain\Balance\BaseBalanceTest;
use Tests\Unit\Helper\File\ImageRepo;

class BalanceTransactionControllerTest extends BaseBalanceTest{

    use DatabaseTransactions;

    public function test_create_transaction()
    {

        $user = User::factory()->create();
        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'), $this->getAmountResponse(300));
        $response->assertStatus(201)
                 ->assertJson([
                    'status' => true,
                    'data' => [
                        'amount' => 300,
                        'status' => TransactionStatus::PENDING->value
                    ]
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'amount',
                        'status',
                        'description',
                        'status'
                    ]
                ]);
    }

    public function test_create_unauthorized()
    {
        $response = $this->postJson(route('balance-transaction-create'), $this->getAmountResponse(300));
        $response->assertStatus(401);
    }

    public function test_create_as_admin_unauthorized()
    {
        $admin = User::where("username", "admin")->first();
        $response = $this->actingAs($admin)
                        ->postJson(route('balance-transaction-create'), $this->getAmountResponse(300));
        $response->assertStatus(403);
    }

    public function test_create_empty_request()
    {

        $user = User::factory()->create();
        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'));
        $response->assertStatus(422);
    }


    public function test_create_invalid_amount()
    {

        $user = User::factory()->create();
        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'), $this->getAmountResponse(-300));
        $response->assertStatus(422);
    }

    public function test_create_invalid_transaction_type()
    {

        $user = User::factory()->create();

        $data =  $this->getAmountResponse(300);
        $data["transaction_type"] = "I";

        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'), $data );
        $response->assertStatus(422);
    }

    public function test_create_empty_description()
    {

        $user = User::factory()->create();

        $data =  $this->getAmountResponse(300);
        unset($data["description"]);

        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'), $data );
        $response->assertStatus(422);
    }


    public function test_create_insuffient_balance()
    {

        $user = User::factory()->create();

        $data =  $this->getAmountResponse(300);
        $data["transaction_type"] = TransactionType::DEBIT->value;

        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'), $data );

        $response->assertStatus(500);
    }

    public function test_accept_balance_transaction()
    {

        $user = User::factory()->create();
        $admin = User::where("username", "admin")->first();


        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'), $this->getAmountResponse(300) );

        $this->assertEquals(0,$this->getBalanceValueByUser($user->id));
        $response->assertStatus(201);

        $paginator = $this->balanceTransactionRepository->getPendingTransactions($user->id);
        $pendingTransaction = $paginator->getItems()[0];
        $this->validSaveModel($pendingTransaction);

        $responseAdmin = $this->actingAs($admin)
                        ->get(
                            route(
                                'balance-transaction-accept',
                                ['transactionId' => $pendingTransaction->id]
                            )
                        );

        $responseAdmin->assertStatus(200);
        $this->assertEquals(300,$this->getBalanceValueByUser($user->id));


    }

    public function test_accept_balance_invalid_transaction()
    {
        $admin = User::where("username", "admin")->first();

        $responseAdmin = $this->actingAs($admin)
                        ->get(
                            route(
                                'balance-transaction-accept',
                                ['transactionId' => 0]
                            )
                        );
        $responseAdmin->assertStatus(500);
    }

    public function test_reject_balance_transaction()
    {

        $user = User::factory()->create();
        $admin = User::where("username", "admin")->first();


        $response = $this->actingAs($user)
                        ->postJson(route('balance-transaction-create'), $this->getAmountResponse(300) );

        $this->assertEquals(0,$this->getBalanceValueByUser($user->id));
        $response->assertStatus(201);
        $paginator = $this->balanceTransactionRepository->getPendingTransactions($user->id);
        $pendingTransaction = $paginator->getItems()[0];
        $this->validSaveModel($pendingTransaction);

        $responseAdmin = $this->actingAs($admin)
                        ->get(
                            route(
                                'balance-transaction-reject',
                                ['transactionId' => $pendingTransaction->id]
                            )
                        );

        $responseAdmin->assertStatus(200);
        $this->assertEquals(0,$this->getBalanceValueByUser($user->id));


    }

    public function test_reject_balance_invalid_transaction()
    {
        $admin = User::where("username", "admin")->first();

        $responseAdmin = $this->actingAs($admin)
                        ->get(
                            route(
                                'balance-transaction-reject',
                                ['transactionId' => 0]
                            )
                        );
        $responseAdmin->assertStatus(500);
    }


    public function getAmountResponse(float $amount, $description = 'Just a new', TransactionType $type = TransactionType::CREDIT){
        $data = [
            'amount' => $amount,
            'description' => $description,
            'transaction_type' => $type->value
        ];
        if($type == TransactionType::CREDIT){
            $data["image"] = ImageRepo::IMAGE_PNG;
        }
        return $data;
    }

}
