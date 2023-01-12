<?php

namespace App\Providers;

use App\Domain\User\Model\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class GateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->userGates();

        $this->adminGates();
    }

    private function userGates()
    {
        Gate::define('create_balance_transaction', function (User $user) {
            return $user->isAdmin() === false;
        });

        Gate::define('last_balance_transaction', function (User $user) {
            return $user->isAdmin() === false;
        });

        Gate::define('credit_balance_transaction', function (User $user) {
            return $user->isAdmin() === false;
        });

        Gate::define('debit_balance_transaction', function (User $user) {
            return $user->isAdmin() === false;
        });

        Gate::define('total_amount_balance_by_transaction_type', function (User $user) {
            return $user->isAdmin() === false;
        });

        Gate::define('get_balance', function (User $user) {
            return $user->isAdmin() === false;
        });
    }

    private function adminGates()
    {
        Gate::define('accept_balance_transaction', function (User $user) {
            return $user->isAdmin() === true;
        });

        Gate::define('reject_balance_transaction', function (User $user) {
            return $user->isAdmin() === true;
        });

        Gate::define('pending_balance_transaction', function (User $user) {
            return $user->isAdmin() === true;
        });
    }
}
