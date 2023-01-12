<?php

namespace App\Providers;

use App\Domain\Balance\Balance\Repository\BalanceRepository;
use App\Domain\Balance\Balance\Repository\BalanceRepositoryInterface;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepository;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepositoryInterface;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Repository\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BalanceRepositoryInterface::class, BalanceRepository::class);
        $this->app->bind(BalanceTransactionRepositoryInterface::class, BalanceTransactionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
