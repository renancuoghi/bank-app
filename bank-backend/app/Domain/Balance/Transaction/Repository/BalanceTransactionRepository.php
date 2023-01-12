<?php

namespace App\Domain\Balance\Transaction\Repository;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Exception\BalanceTransactionException;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\Shared\Helper\Paginator;
use App\Domain\User\Model\User;
use Illuminate\Database\Query\Builder;

class BalanceTransactionRepository implements BalanceTransactionRepositoryInterface
{
    /**
     * Create a new balance transaction
     *
     * @param array<mixed> $data
     */
    public function create(array $data): ?BalanceTransaction
    {
        return BalanceTransaction::create($data);
    }
    /**
     * Updates an existing balance transaction
     *
     * @param int $id
     * @param array<mixed> $data
     */
    public function update(int $id, array $data): ?BalanceTransaction
    {
        if (BalanceTransaction::whereId($id)->update($data)) {
            return $this->getById($id);
        }
        return null;
    }
    /**
     * Get user by id
     *
     * @param int $id
     */
    public function getById(int $id): ?BalanceTransaction
    {
        return BalanceTransaction::find($id);
    }

    /**
     * Approve a transaction
     *
     * @param int $id
     * @param User $admin
     * @return bool
     */
    public function approve(BalanceTransaction $balanceTransaction, User $admin): bool
    {
        return $this->changeStatus($balanceTransaction, $admin, TransactionStatus::ACCEPTED);
    }

    /**
     * Reject a transaction
     *
     * @param int $id
     * @param User $admin
     * @return bool
     */
    public function reject(BalanceTransaction $balanceTransaction, User $admin): bool
    {
        return $this->changeStatus($balanceTransaction, $admin, TransactionStatus::REJECTED);
    }

    /**
     * Accept or reject a transaction only a admin can do it
     *
     * @param int $id
     * @param User $admin
     * @param TransactionStatus $status
     *
     * @return bool
     */
    protected function changeStatus(BalanceTransaction $transaction, User $admin, TransactionStatus $status)
    {
        if ($admin->isAdmin()) {
            $transaction->status = $status->value;
            $transaction->approved_user_id = $admin->id;
            return $transaction->save();
        }
        throw new BalanceTransactionException('Only a admin can reject or approve a Balance transaction');
    }
    /**
     * Return a paginate of all pending transaction from one user
     *
     * @param int $userId
     * @int $page
     * @int $pageSize
     *
     * @return Paginator
     */
    public function getPendingTransactions(int $userId = null, int $page = 1, int $pageSize = 10): Paginator
    {
        $query = BalanceTransaction::with('user')->where('status', TransactionStatus::PENDING->value);
        if (isset($userId)) {
            $query->where('user_id', $userId);
        }

        $total = $query->count();
        $data = BalanceTransaction::paginate(
            $query->orderBy('created_at', 'asc'),
            $page,
            $pageSize
        )->get();

        return new Paginator($data, $total, $pageSize);
    }

    /**
     * Return a paginate of last transactions (accept) from one user by Year and month
     *
     * @param int $userId
     * @param \Datetime $date
     * @int $page
     * @int $pageSize
     *
     * @return Paginator
     */
    public function getLastTransactions(int $userId, TransactionStatus $status = TransactionStatus::ACCEPTED, \DateTime $date, int $page = 0, int $pageSize = 10, TransactionType $type = null): Paginator
    {
        $query = BalanceTransaction::createQueryTransactions($userId, $status, $date, $type);
        $total = $query->count();
        $data = BalanceTransaction::paginate(
            $query->orderBy('created_at', 'desc'),
            $page,
            $pageSize
        )->get();
        return new Paginator($data, $total, $pageSize);
    }

    /**
     * Return a paginate of last credits (accept) from one user by Year and month
     *
     * @param int $userId
     * @param \Datetime $date
     * @int $page
     * @int $pageSize
     *
     * @return Paginator
     */
    public function getLastCreditTransactions(int $userId, TransactionStatus $status = TransactionStatus::ACCEPTED, \DateTime $date, int $page = 0, int $pageSize = 10): Paginator
    {
        return $this->getLastTransactions($userId, $status, $date, $page, $pageSize, TransactionType::CREDIT);
    }

    /**
     * Return a paginate of last debit (accept) from one user by Year and month
     *
     * @param int $userId
     * @param \Datetime $date
     * @int $page
     * @int $pageSize
     *
     * @return Paginator
     */
    public function getLastDebitTransactions(int $userId, \DateTime $date, int $page = 0, int $pageSize = 10): Paginator
    {
        return $this->getLastTransactions($userId, TransactionStatus::ACCEPTED, $date, $page, $pageSize, TransactionType::DEBIT);
    }

    public function getSumAmountByTransactionType(int $userId, \DateTime $date, TransactionType $type): float
    {
        return BalanceTransaction::getSumAmountByTransactionType($userId, $date, $type);
    }

    public function getTransactionById(int $transactionId)
    {
        return BalanceTransaction::with('user')->find($transactionId);
    }
}
