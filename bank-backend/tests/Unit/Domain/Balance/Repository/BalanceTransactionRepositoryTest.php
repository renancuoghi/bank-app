<?php
namespace Tests\Unit\Domain\Balance\Repository;

use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Exception\BalanceTransactionException;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\User\Model\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Unit\Domain\Balance\BaseBalanceTest;

class BalanceTransactionRepositoryTest extends BaseBalanceTest{

    use DatabaseTransactions;

    public function test_insert_balance_transaction(){

        $user = $this->createUser();
        $balance = $this->balanceRepository->createOrGetByUserId($user->id);

        $this->createBalanceTransaction($user->id);

    }


    public function test_get_balance_transaction(){

        $user = $this->createUser();
        $balance = $this->balanceRepository->createOrGetByUserId($user->id);

        $balanceTransaction =  $this->createBalanceTransaction($user->id);

        $transaction = $this->balanceTransactionRepository->getById($balanceTransaction->id);
        $this->validSaveModel($transaction);
        $this->assertEquals($balanceTransaction->id, $transaction->id);

    }


    public function test_accept_transaction(){

        $user = $this->createUser();
        $admin = $this->createUser(true);

        $balanceTransaction = $this->createBalanceTransaction($user->id);

        $this->assertEquals(TransactionStatus::PENDING->value, $balanceTransaction->status);
        $this->assertNull($balanceTransaction->approved_user_id);

        $this->assertTrue($this->balanceTransactionRepository->approve($balanceTransaction, $admin));

        $transaction = $this->balanceTransactionRepository->getById($balanceTransaction->id);
        $this->assertEquals(TransactionStatus::ACCEPTED->value, $transaction->status);
        $this->assertEquals($admin->id, $transaction->approved_user_id);

    }

    public function test_reject_transaction(){

        $user = $this->createUser();
        $admin = $this->createUser(true);


        $balanceTransaction = $this->createBalanceTransaction($user->id);

        $this->assertEquals(TransactionStatus::PENDING->value, $balanceTransaction->status);
        $this->assertNull($balanceTransaction->approved_user_id);

        $this->assertTrue($this->balanceTransactionRepository->reject($balanceTransaction, $admin));

        $transaction = $this->balanceTransactionRepository->getById($balanceTransaction->id);
        $this->assertEquals(TransactionStatus::REJECTED->value, $transaction->status);
        $this->assertEquals($admin->id, $transaction->approved_user_id);

    }


    public function test_non_admin_accept_transaction() {
        $this->expectException(BalanceTransactionException::class);

        $user = $this->createUser();

        $balance = $this->balanceRepository->createOrGetByUserId($user->id);

        $amount = 100;

        $balanceTransaction = $this->createBalanceTransaction($user->id);

        $this->assertTrue($this->balanceTransactionRepository->approve($balanceTransaction, $user));
    }

    public function testPendingTransactions(){


        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->createBalanceTransaction($user->id);
        $this->createBalanceTransaction($user->id);

        $this->createBalanceTransaction($user2->id);

        $paginator = $this->balanceTransactionRepository->getPendingTransactions($user->id);

        $this->assertEquals(2, $paginator->getTotal());

        $paginator2 = $this->balanceTransactionRepository->getPendingTransactions($user2->id);

        $this->assertEquals(1, $paginator2->getTotal());
    }

    public function testPendingTransactionsManyUsers(){

        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->createBalanceTransaction($user->id);
        $this->createBalanceTransaction($user->id);
        $this->createBalanceTransaction($user2->id);

        $paginator = $this->balanceTransactionRepository->getPendingTransactions();

        $this->assertGreaterThanOrEqual(3, $paginator->getTotal());

    }

    public function testLastTransactions(){

        $user = $this->createUser();
        $admin = $this->createUser(true);

        $balanceTransaction = $this->createBalanceTransaction($user->id);

        $this->assertTrue($this->balanceTransactionRepository->approve($balanceTransaction, $admin));

        $paginator = $this->balanceTransactionRepository->getLastTransactions($user->id, TransactionStatus::ACCEPTED, Carbon::now());

        $this->assertEquals(1, $paginator->getTotal());

        $this->createDebit($user->id, 30);
        $this->createDebit($user->id, 50);
        $this->createDebit($user->id, 10);

        $paginator = $this->balanceTransactionRepository->getLastTransactions($user->id, TransactionStatus::ACCEPTED, Carbon::now());
        $this->assertEquals(4, $paginator->getTotal());
    }

    public function testLastCreditsTransactions(){

        $user = $this->createUser();
        $admin = $this->createUser(true);

        $balanceTransaction = $this->createBalanceTransaction($user->id);
        $balanceTransaction2 = $this->createBalanceTransaction($user->id);

        $this->assertTrue($this->balanceTransactionRepository->approve($balanceTransaction, $admin));


        $paginator = $this->balanceTransactionRepository->getLastCreditTransactions($user->id, TransactionStatus::ACCEPTED, Carbon::now());

        $this->assertEquals(1, $paginator->getTotal());
        //acception another one
        $this->assertTrue($this->balanceTransactionRepository->approve($balanceTransaction2, $admin));

        $paginator = $this->balanceTransactionRepository->getLastCreditTransactions($user->id, TransactionStatus::ACCEPTED, Carbon::now());

        $this->assertEquals(2, $paginator->getTotal());

    }

    public function testLastDebitsTransactions(){

        $user = $this->createUser();
        $admin = $this->createUser(true);

        $balanceTransaction = $this->createBalanceTransaction($user->id);

        $this->assertTrue($this->balanceTransactionRepository->approve($balanceTransaction, $admin));

        $this->createDebit($user->id, 30);
        $this->createDebit($user->id, 50);
        $this->createDebit($user->id, 10);

        $paginator = $this->balanceTransactionRepository->getLastDebitTransactions($user->id, Carbon::now());

        $this->assertEquals(3, $paginator->getTotal());


    }

    public function testLastTransactionsInLastMonth(){

        $user = $this->createUser();
        $admin = $this->createUser(true);
        $currentDate = Carbon::now();

        // last month
        $this->travel(-1)->months();
        $this->assertTrue($this->balanceTransactionRepository->approve($this->createBalanceTransaction($user->id), $admin));
        $this->assertTrue($this->balanceTransactionRepository->approve($this->createBalanceTransaction($user->id), $admin));

        $paginator = $this->balanceTransactionRepository->getLastTransactions($user->id, TransactionStatus::ACCEPTED, $currentDate);
        $this->assertEquals(0, $paginator->getTotal());

        $paginatorLastMonth = $this->balanceTransactionRepository->getLastTransactions($user->id, TransactionStatus::ACCEPTED, Carbon::now());
        $this->assertEquals(2, $paginatorLastMonth->getTotal());

    }

    public function testPaginationLastTransactions(){

        $user = $this->createUser();
        $admin = $this->createUser(true);
        // primeira transaction one day ago

        $balanceTransaction = $this->createBalanceTransaction($user->id);
        $lastId = $balanceTransaction->id;


        $this->assertTrue($this->balanceTransactionRepository->approve($balanceTransaction, $admin));
        // creating many transactions going to the feature
        for($i=0;$i<100; $i++){
            $this->travel(5)->minutes();
            $this->assertTrue($this->balanceTransactionRepository->approve($this->createBalanceTransaction($user->id), $admin));

        }
        // int $userId, TransactionStatus $status = TransactionStatus::ACCEPTED, \DateTime $date, int $page = 0, int $pageSize = 10, TransactionType $type = null)
        $paginator = $this->balanceTransactionRepository->getLastTransactions($user->id, TransactionStatus::ACCEPTED, Carbon::now());
        $this->assertEquals(101, $paginator->getTotal());
        // last transaction
        $transaction = $paginator->getItems()[0];
        $this->assertNotEquals($lastId, $transaction->id);
        $this->assertGreaterThan($lastId, $transaction->id);
        $this->assertEquals(11, $paginator->getTotal_pages());
        // DB::enableQueryLog();
        $paginatorLastPage = $this->balanceTransactionRepository->getLastTransactions($user->id, TransactionStatus::ACCEPTED, Carbon::now(), 11);
        // print_r(DB::getQueryLog());
        $data = $paginatorLastPage->getItems()->toArray();

        $lastTransaction = $data[count($data) - 1];
        $this->assertEquals($lastId, $lastTransaction["id"]);

    }


    public function testTotalCreditBalance(){

        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertTrue($this->balanceTransactionRepository->approve($this->createBalanceTransaction($user->id), $admin));
        $this->assertTrue($this->balanceTransactionRepository->approve($this->createBalanceTransaction($user->id), $admin));

        $this->assertEquals(200, $this->balanceTransactionRepository->getSumAmountByTransactionType($user->id, Carbon::now(),TransactionType::CREDIT));


    }

    public function testTotalDebitBalance(){

        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertTrue($this->balanceTransactionRepository->approve($this->createBalanceTransaction($user->id), $admin));

        $this->createDebit($user->id, 23);
        $this->createDebit($user->id, 27);
        $this->createDebit($user->id, 10);
        $this->assertEquals(100, $this->balanceTransactionRepository->getSumAmountByTransactionType($user->id, Carbon::now(),TransactionType::CREDIT));
        $this->assertEquals(60, $this->balanceTransactionRepository->getSumAmountByTransactionType($user->id, Carbon::now(),TransactionType::DEBIT));


    }

    private function createBalanceTransaction($userId, $status = TransactionStatus::PENDING, $type = TransactionType::CREDIT, float $amount = 100) : BalanceTransaction
    {
        $balance = $this->balanceRepository->createOrGetByUserId($userId);

        $balanceTransaction = $this->balanceTransactionRepository->create(
            [
                'user_id' => $userId,
                'balance_id' => $balance->id,
                'amount' => $amount,
                'transaction_type' => $type->value,
                'description' => 'just a transaction',
                'status' => $status->value,
            ]
        );

        $this->validSaveModel($balanceTransaction);

        return $balanceTransaction;

    }

    private function createDebit($userId, float $amount = 10){
        return $this->createBalanceTransaction($userId, TransactionStatus::ACCEPTED, TransactionType::DEBIT, $amount);
    }


}
