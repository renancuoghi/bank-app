<?php

declare(strict_types=1);

namespace App\Domain\Balance\Transaction\Enum;

enum TransactionType: string
{
    case CREDIT = 'C';
    case DEBIT = 'D';
}
