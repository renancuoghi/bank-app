<?php

namespace App\Domain\Balance\Balance\Repository;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionType;

class BalanceRepository implements BalanceRepositoryInterface
{
    /**
     * Create a new balance
     *
     * @param array<mixed> $data
     */
    public function create(array $data): ?Balance
    {
        return Balance::create($data);
    }
    /**
     * Updates an existing balance
     *
     * @param int $id
     * @param array<mixed> $data
     */
    public function update(int $id, array $data): ?Balance
    {
        if (Balance::whereId($id)->update($data)) {
            return $this->getById($id);
        }
        return null;
    }
    /**
     * Get balance by id
     *
     * @param int $id
     */
    public function getById(int $id): ?Balance
    {
        return Balance::find($id);
    }


    /**
     * Create a new balance for a user
     * The default for a balance is a zero account balance
     * Each user can only have one balance account
     *
     * @param int userId
     *
     * @return Balance
     */
    public function createOrGetByUserId(int $userId): ?Balance
    {
        $balance = Balance::where('user_id', $userId)->first();
        if (!$balance) {
            return Balance::create([
                'user_id' => $userId,
                'total' => 0
            ]);
        }
        return $balance;
    }

    /**
     * Get a balance by id and lock for transaction
     *
     * @param int $id
     */
    public function getAndLock(int $id): ?Balance
    {
        return Balance::whereId($id)->lockForUpdate()->first();
    }

    /**
     * Change a total amount from a balance (increase or decrease)
     *
     * @param Balance $balance
     * @param float $amount
     * @param TransactionType $type
     * @return bool
     */
    public function updateBalanceAmount(Balance $balance, float $amount, TransactionType $type): bool
    {
        if ($type == TransactionType::CREDIT) {
            $balance->total += $amount;
        } else {
            $balance->total -= $amount;
        }
        return $balance->save();
    }
}
