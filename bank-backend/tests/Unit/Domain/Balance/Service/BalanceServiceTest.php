<?php
namespace Tests\Unit\Domain\Balance\Repository;

use App\Domain\Balance\Balance\Exception\InsufficientBalanceException;
use App\Domain\Balance\Balance\Exception\InvalidBalanceTransactionException;
use App\Domain\Balance\Balance\Service\BalanceService;
use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\User\Model\User;
use Tests\Unit\Domain\Balance\BaseBalanceTest;

class BalanceServiceTest extends BaseBalanceTest{

    private $imageUrl = "https://www.google.com.br/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png";

    public function test_create_transaction(){
        $user = $this->createUser();

        $transaction = $this->balanceService->createTransaction($user, 100, 'test', TransactionType::CREDIT, $this->imageUrl);
        $this->validSaveModel($transaction);
        $this->assertEquals(100, $transaction->amount);

    }

    public function test_credit_approval_process(){
        $user = $this->createUser();
        
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));

        $this->createBalanceWithValue(100, $user);

        // new value 100
        $this->assertEquals(100, $this->getBalanceValueByUser($user->id));
        
    }


    public function test_more_one_credit_approval_process(){
        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));

        $this->createBalanceWithValue(100, $user, $admin);

        $this->createBalanceWithValue(600, $user, $admin);
        // new value 700
        $this->assertEquals(700, $this->getBalanceValueByUser($user->id));    
    }


    public function test_debit_approval_process(){
        $user = $this->createUser();
        
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));

        $this->createBalanceWithValue(100, $user);

        // new value 100
        $this->assertEquals(100, $this->getBalanceValueByUser($user->id));
        
        // remove 70
        $transaction = $this->balanceService->createTransaction($user, 70, 'test', TransactionType::DEBIT);
        $this->validSaveModel($transaction);
        $this->assertEquals(TransactionType::DEBIT->value, $transaction->transaction_type);
        $this->assertEquals(TransactionStatus::ACCEPTED->value, $transaction->status);
        // new balance amount must be 30
        $this->assertEquals(30, $this->getBalanceValueByUser($user->id));
        // more one debit
        $transaction2 = $this->balanceService->createTransaction($user, 30, 'test', TransactionType::DEBIT);
        $this->validSaveModel($transaction2);
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));
    }


    public function test_negative_debit_user(){
        $this->expectException(InsufficientBalanceException::class);

        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));

        $this->createBalanceWithValue(100, $user, $admin);
        $transaction = $this->balanceService->createTransaction($user, 70, 'test', TransactionType::DEBIT);
        $this->validSaveModel($transaction);
        $this->assertEquals(30, $this->getBalanceValueByUser($user->id));
        // exception
        $this->balanceService->createTransaction($user, 40, 'test', TransactionType::DEBIT);        
    }


    public function test_many_transactions(){
        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));

        $this->createBalanceWithValue(100, $user, $admin);
        $this->balanceService->createTransaction($user, 60, 'test', TransactionType::DEBIT);
        $this->assertEquals(40, $this->getBalanceValueByUser($user->id));
        $this->balanceService->createTransaction($user, 15, 'test', TransactionType::DEBIT);
        $this->assertEquals(25, $this->getBalanceValueByUser($user->id));
        $this->createBalanceWithValue(340, $user, $admin);
        // final balance value
        $this->assertEquals(365, $this->getBalanceValueByUser($user->id));
    }

    public function test_reject_transaction(){
        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));
        $transaction = $this->balanceService->createTransaction($user, 200, 'test', TransactionType::CREDIT, $this->imageUrl);
        $this->validSaveModel($transaction);
        // check is the same because is pending
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));
        $this->assertTrue($this->balanceService->rejectTransaction($transaction->id, $admin));

    }

    public function test_twice_approve_same_transaction(){
        $this->expectException(InvalidBalanceTransactionException::class);

        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));
        $transaction = $this->balanceService->createTransaction($user, 200, 'test', TransactionType::CREDIT, $this->imageUrl);
        $this->assertTrue($this->balanceService->acceptTransaction($transaction->id, $admin));
        // exception
        $this->assertTrue($this->balanceService->acceptTransaction($transaction->id, $admin));
    }

    public function test_twice_reject_same_transaction(){
        $this->expectException(InvalidBalanceTransactionException::class);

        $user = $this->createUser();
        $admin = $this->createUser(true);
        $this->assertEquals(0, $this->getBalanceValueByUser($user->id));
        $transaction = $this->balanceService->createTransaction($user, 200, 'test', TransactionType::CREDIT);
        $this->assertTrue($this->balanceService->rejectTransaction($transaction->id, $admin));
        // exception
        $this->assertTrue($this->balanceService->rejectTransaction($transaction->id, $admin));
    }

    public function test_create_credit_transaction_without_image(){
        $this->expectException(InvalidBalanceTransactionException::class);
        $user = $this->createUser(); 

        $transaction = $this->balanceService->createTransaction($user, 100, 'test', TransactionType::CREDIT);
        $this->validSaveModel($transaction);
        $this->assertEquals(100, $transaction->amount);

    }

    private function createBalanceWithValue(float $amount, User $user, $adminUser = null) 
    {
        if(isset($adminUser)){
            $admin = $adminUser;  
        }else{
            $admin = $this->createUser(true);
        }        
        // add new credit        
        $transaction = $this->balanceService->createTransaction($user, $amount, 'test', TransactionType::CREDIT, $this->imageUrl);
        $this->validSaveModel($transaction);

        $this->assertTrue($this->balanceService->acceptTransaction($transaction->id, $admin));

    }



}