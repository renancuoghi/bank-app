<?php

namespace App\Domain\Balance\Transaction\Service;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\User\Model\User;
use DateTime;

interface BalanceTransactionServiceInterface
{
    public function createBalanceTransaction(Balance $balance, User $user, float $amount, string $description, TransactionType $type, ?string $pathImage = null, ?DateTime $created_at = null): BalanceTransaction;

    public function isNegativeBalanceTransaction(Balance $balance, float $amount, TransactionType $type): bool;
}
