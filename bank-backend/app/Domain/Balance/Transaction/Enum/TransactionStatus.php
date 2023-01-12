<?php

declare(strict_types=1);

namespace App\Domain\Balance\Transaction\Enum;

enum TransactionStatus: string
{
    case PENDING = 'P';
    case ACCEPTED = 'A';
    case REJECTED = 'R';
}
