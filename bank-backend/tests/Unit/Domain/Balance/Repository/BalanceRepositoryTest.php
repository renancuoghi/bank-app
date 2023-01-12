<?php
namespace Tests\Unit\Domain\Balance\Repository;

use Tests\Unit\Domain\Balance\BaseBalanceTest;

class BalanceRepositoryTest extends BaseBalanceTest{


    public function test_insert_balance(){
        
        $user = $this->createUser();

        $balance = $this->createOrGetBalance($user->id);
        $this->assertNotNull($balance);
        $this->assertGreaterThan(0, $balance->id);


    }

    public function test_balance_is_the_same(){
        
        $user = $this->createUser();
        $balance = $this->createOrGetBalance($user->id);
        $this->assertNotNull($balance);
        $this->assertGreaterThan(0, $balance->id);

        // must be the same
        $getBalance = $this->createOrGetBalance($user->id);
        $this->assertNotNull($getBalance);
        $this->assertEquals($balance->id, $getBalance->id);

    }

}