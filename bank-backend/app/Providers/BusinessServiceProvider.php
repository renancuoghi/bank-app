<?php

namespace App\Providers;

use App\Domain\Balance\Balance\Service\BalanceService;
use App\Domain\Balance\Balance\Service\BalanceServiceInterface;
use App\Domain\Balance\Transaction\Service\BalanceTransactionService;
use App\Domain\Balance\Transaction\Service\BalanceTransactionServiceInterface;
use App\Domain\User\Service\UserService;
use App\Domain\User\Service\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class BusinessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(BalanceTransactionServiceInterface::class, BalanceTransactionService::class);
        $this->app->singleton(BalanceServiceInterface::class, BalanceService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
