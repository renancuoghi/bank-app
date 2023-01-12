<?php

namespace App\Domain\Balance\Balance\Repository;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Shared\Interface\RepositoryInterface;

interface BalanceRepositoryInterface extends RepositoryInterface
{
    /**
     * Get or create a new balance
     *
     * @param array<mixed> $data
     */
    public function createOrGetByUserId(int $user_id): ?Balance;

    /**
     * Get a balance by id and lock for transaction
     *
     * @param int $id
     */
    public function getAndLock(int $id): ?Balance;

    /**
     * Change a total amount from a balance
     */
    public function updateBalanceAmount(Balance $balance, float $amount, TransactionType $type): bool;
}
