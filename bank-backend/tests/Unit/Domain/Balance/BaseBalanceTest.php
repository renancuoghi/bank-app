<?php
namespace Tests\Unit\Domain\Balance;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Balance\Repository\BalanceRepository;
use App\Domain\Balance\Balance\Repository\BalanceRepositoryInterface;
use App\Domain\Balance\Balance\Service\BalanceService;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepository;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepositoryInterface;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Repository\UserRepositoryInterface;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Unit\Domain\User\Helper\UserHelperTest;

abstract class BaseBalanceTest extends TestCase{

    use DatabaseTransactions;

    protected UserRepositoryInterface $userRepository;

    protected BalanceRepositoryInterface $balanceRepository;

    protected BalanceTransactionRepositoryInterface $balanceTransactionRepository;

    protected BalanceService $balanceService;


    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(UserRepository::class);
        $this->balanceRepository = $this->app->make(BalanceRepository::class);
        $this->balanceTransactionRepository = $this->app->make(BalanceTransactionRepository::class);
        $this->balanceService = $this->app->make(BalanceService::class);
    }


    protected function createUser($admin = false){
        $data = [];
        if($admin === true){
            $data = UserHelperTest::getDefaultUserData($admin,'adminn', 'adminn@admin.com');
        }else{
            $data = UserHelperTest::getDefaultUserData();
        }
        $user = $this->userRepository->create($data);
        $this->validSaveModel($user);
        return $user;
    }

    protected function createOrGetBalance(int $user_id) : Balance
    {
        $balance = $this->balanceRepository->createOrGetByUserId($user_id);
        $this->validSaveModel($balance);
        return $balance;
    }

    protected function getBalanceValueByUser(int $user_id) : float
    {
        $balance = $this->createOrGetBalance($user_id);
        return $balance->total;
    }

    protected function validSaveModel($model){
        $this->assertNotNull($model);
        $this->assertGreaterThan(0, $model->id);
    }

}