<?php

namespace App\Domain\Balance\Transaction\Service;

use App\Domain\Balance\Balance\Exception\InsufficientBalanceException;
use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepository;
use App\Domain\User\Model\User;
use Carbon\Carbon;

class BalanceTransactionService implements BalanceTransactionServiceInterface
{
    private BalanceTransactionRepository $balanceTransactionRepository;

    public function __construct(BalanceTransactionRepository $balanceTransactionRepository)
    {
        $this->balanceTransactionRepository = $balanceTransactionRepository;
    }

    /**
     * Create balance transaction
     * 1 - Check if balance has value (if is a debit)
     * 2 - create transaction data, if is a DEBIT transaction will be accepted automatically and credit needs to be accepted
     *
     * @param Balance $balance
     * @param User $user
     * @param float $amount
     * @param string $description
     * @param TransactionType $type
     *
     * @return BalanceTransaction
     */
    public function createBalanceTransaction(Balance $balance, User $user, float $amount, string $description, TransactionType $type, ?string $pathImage = null, ?\DateTime $created_at = null): BalanceTransaction
    {
        if (isset($created_at) === false) {
            $created_at = Carbon::now();
        }
        if ($this->isNegativeBalanceTransaction($balance, $amount, $type)) {
            $data = [
                'user_id' => $user->id,
                'balance_id' => $balance->id,
                'amount' => $amount,
                'transaction_type' => $type->value,
                'description' => $description,
                'status' => ($type == TransactionType::DEBIT) ? TransactionStatus::ACCEPTED->value : TransactionStatus::PENDING->value,
                'path_image' => $pathImage,
                'created_at' => $created_at
            ];

            return $this->balanceTransactionRepository->create($data);
        }
        throw new InsufficientBalanceException('Balance doesn\'t have enough funds for this transaction');
    }

    /**
     * Verify if a transaction has amount to avoid negative balance
     *
     * @param User $user
     * @param float $amount
     * @param TransactionType $type
     */
    public function isNegativeBalanceTransaction(Balance $balance, float $amount, TransactionType $type): bool
    {
        $totalBalance = $balance->total;

        if (TransactionType::DEBIT == $type) {
            return $totalBalance >= $amount;
        }
        return true;
    }
}
