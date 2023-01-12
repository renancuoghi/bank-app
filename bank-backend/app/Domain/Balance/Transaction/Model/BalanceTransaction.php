<?php

namespace App\Domain\Balance\Transaction\Model;

use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Shared\Model\BaseModel;
use App\Domain\User\Model\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class BalanceTransaction extends BaseModel
{
    use HasFactory;

    protected $table = 'balance_transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'transaction_type',
        'status',
        'approved_user_id',
        'balance_id',
        'path_image',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function balance()
    {
        return $this->belongsTo(Balance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function isPendingCreditTransaction(): bool
    {
        return $this->status == TransactionStatus::PENDING->value
                && $this->transaction_type == TransactionType::CREDIT->value;
    }

    public static function createQueryCreatedAtByMonthYear(\DateTime $date): Builder
    {
        return self::whereYear('created_at', '=', $date->format('Y'))
                    ->whereMonth('created_at', '=', $date->format('m'));
    }

    public static function createQueryTransactions(int $userId, TransactionStatus $status = TransactionStatus::ACCEPTED, \DateTime $date, TransactionType $type = null): Builder
    {
        $query = self::createQueryForUserAndDate($userId, $date)
                        ->where('status', $status->value);

        if (isset($type)) {
            $query = $query->where('transaction_type', $type->value);
        }
        return $query;
    }

    public static function createQueryForUserAndDate(int $userId, \DateTime $date): Builder
    {
        return BalanceTransaction::createQueryCreatedAtByMonthYear($date)
                    ->where('user_id', $userId);
    }

    public static function getSumAmountByTransactionType(int $userId, \DateTime $date, TransactionType $type): float
    {
        $result = self::selectRaw("SUM(amount) as amount")
                    ->where('transaction_type', $type->value)
                    ->where('user_id', $userId)
                    ->where('status', TransactionStatus::ACCEPTED->value)
                    ->whereYear('created_at', '=', $date->format('Y'))
                    ->whereMonth('created_at', '=', $date->format('m'))
                    ->get()->first();

        return floatval($result->amount);
    }
}
