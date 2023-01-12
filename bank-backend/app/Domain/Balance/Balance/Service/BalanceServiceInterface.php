<?php

namespace App\Domain\Balance\Balance\Service;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\User\Model\User;
use DateTime;

interface BalanceServiceInterface
{
    public function createTransaction(User $user, float $amount, string $description, TransactionType $type, ?string $imagePath = null, ?DateTime $created_at = null): BalanceTransaction;

    public function acceptTransaction(int $transactionId, User $admin): bool;

    public function rejectTransaction(int $transactionId, User $admin): bool;
}
