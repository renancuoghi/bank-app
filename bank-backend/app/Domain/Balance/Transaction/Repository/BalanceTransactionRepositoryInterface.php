<?php

namespace App\Domain\Balance\Transaction\Repository;

use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\Shared\Helper\Paginator;
use App\Domain\Shared\Interface\RepositoryInterface;
use App\Domain\User\Model\User;

interface BalanceTransactionRepositoryInterface extends RepositoryInterface
{
    public function approve(BalanceTransaction $balanceTransaction, User $admin): bool;

    public function reject(BalanceTransaction $balanceTransaction, User $admin): bool;

    public function getPendingTransactions(int $userId = null, int $page = 1, int $pageSize = 10): Paginator;

    public function getLastTransactions(int $userId, TransactionStatus $status = TransactionStatus::ACCEPTED, \DateTime $date, int $page = 0, int $pageSize = 10, TransactionType $type = null): Paginator;

    public function getLastCreditTransactions(int $userId, TransactionStatus $status = TransactionStatus::ACCEPTED, \DateTime $date, int $page = 0, int $pageSize = 10): Paginator;

    public function getLastDebitTransactions(int $userId, \DateTime $date, int $page = 0, int $pageSize = 10): Paginator;

    public function getSumAmountByTransactionType(int $userId, \DateTime $date, TransactionType $type): float;

    public function getTransactionById(int $transactionId);
}
