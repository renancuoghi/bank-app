<?php

namespace App\Domain\Balance\Balance\Service;

use App\Domain\Balance\Balance\Exception\InsufficientBalanceException;
use App\Domain\Balance\Balance\Exception\InvalidBalanceException;
use App\Domain\Balance\Balance\Exception\InvalidBalanceTransactionException;
use App\Domain\Balance\Balance\Exception\InvalidUserException;
use App\Domain\Balance\Balance\Model\Balance;
use App\Domain\Balance\Balance\Repository\BalanceRepositoryInterface;
use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Model\BalanceTransaction;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepositoryInterface;
use App\Domain\Balance\Transaction\Service\BalanceTransactionServiceInterface;
use App\Domain\Shared\Service\ServiceBase;
use App\Domain\User\Model\User;
use DateTime;
use Illuminate\Support\Facades\DB;

/**
 * Balance service has a responsibility to manage all transactions
 * When a transaction is accepted, this class will lead to increase or decrease the total amount of the balance
 * Used a lock transaction to prevent multiple decreasing or increasing
 * because we are working with a critital business rule (money balance)
 */
class BalanceService extends ServiceBase implements BalanceServiceInterface
{
    private BalanceRepositoryInterface $balanceRepository;

    private BalanceTransactionRepositoryInterface $balanceTransactionRepository;

    private BalanceTransactionServiceInterface $balanceTransactionService;


    /**
     * Constructor
     *
     * @param BalanceRepositoryInterface $balanceRepository
     * @param BalanceTransactionRepositoryInterface $balanceTransactionRepository
     */
    public function __construct(
        BalanceRepositoryInterface $balanceRepository,
        BalanceTransactionRepositoryInterface $balanceTransactionRepository,
        BalanceTransactionServiceInterface $balanceTransactionService
    ) {
        $this->balanceRepository = $balanceRepository;
        $this->balanceTransactionRepository = $balanceTransactionRepository;
        $this->balanceTransactionService = $balanceTransactionService;
    }

    /**
     * Create a new transaction
     * 1 - create or get a balance
     * 2 - check if is not a admin user
     * 3 - create a balance lock
     * 4 - create a new balance transaction
     * 5 - change balance total amount if is applicable
     *
     * @param User $user
     * @param float $amount
     * @param string $description
     * @param TransactionType $type
     *
     * @return BalanceTransaction
     */
    public function createTransaction(User $user, float $amount, string $description, TransactionType $type, ?string $pathImage = null, ?DateTime $created_at = null): BalanceTransaction
    {
        $balance = $this->checkAndGetBalance($user, $type, $pathImage);

        DB::beginTransaction();
        if (TransactionType::DEBIT == $type) { // in case of debit get a balance as a lock because it shall change its total amount
            $lockedBalance = $this->balanceRepository->getAndLock($balance->id);
            $balanceTransaction = $this->balanceTransactionService->createBalanceTransaction($lockedBalance, $user, $amount, $description, $type, $pathImage, $created_at);
            if ($balanceTransaction->status == TransactionStatus::ACCEPTED->value) {
                $this->updateBalanceTotalAmount($lockedBalance, $balanceTransaction);
            }
        } else { // credit need to be approved to change value
            $balanceTransaction = $this->balanceTransactionService->createBalanceTransaction($balance, $user, $amount, $description, $type, $pathImage, $created_at);
        }
        DB::commit();
        return $balanceTransaction;
    }

    /**
     * Approve a transaction and update the balance amount
     *
     * @param int $transactionId
     * @param User $admin
     *
     * @return bool
     */
    public function acceptTransaction(int $transactionId, User $admin): bool
    {
        $balanceTransaction = $this->balanceTransactionRepository->getById($transactionId);
        if ($balanceTransaction && $balanceTransaction->isPendingCreditTransaction()) {
            DB::beginTransaction();
            $lockedBalance = $this->balanceRepository->getAndLock($balanceTransaction->balance_id);
            if ($this->balanceTransactionRepository->approve($balanceTransaction, $admin)) {
                $this->updateBalanceTotalAmount($lockedBalance, $balanceTransaction);
            }
            DB::commit();
            return true;
        }
        throw new InvalidBalanceTransactionException('Invalid transaction');
    }
    /**
     * Reject a balance transaction
     *
     * @param int $transactionId
     * @param User $admin
     *
     * @return bool
     */
    public function rejectTransaction(int $transactionId, User $admin): bool
    {
        $balanceTransaction = $this->balanceTransactionRepository->getById($transactionId);
        if ($balanceTransaction && $balanceTransaction->isPendingCreditTransaction()) {
            return $this->balanceTransactionRepository->reject($balanceTransaction, $admin);
        }
        throw new InvalidBalanceTransactionException('Invalid transaction');
    }


    /**
     * Update the total balance amount of a accepted transaction
     *
     * @param Balance $balance
     * @param BalanceTransaction $transaction
     *
     */
    private function updateBalanceTotalAmount(Balance $balance, BalanceTransaction $balanceTransaction): void
    {
        $type = TransactionType::from($balanceTransaction->transaction_type);
        if (!$this->balanceRepository->updateBalanceAmount($balance, $balanceTransaction->amount, $type)) {
            throw new InvalidBalanceException('There was an error updating the balance, please try again.');
        }
    }

    /**
     * Check some validation on transaction and get current balance
     * 1 - Check image (credit is obrigatory a image)
     * 2 - check if user has a balance
     * 3 - check if user is not a admin
     *
     * @return Balance
     */
    private function checkAndGetBalance(User $user, TransactionType $type, ?string $pathImage = null): Balance
    {
        // check image
        $this->checkImage($type, $pathImage);
        // check if user has a balance and create a new if doesn't exist
        $balance = $this->balanceRepository->createOrGetByUserId($user->id);
        if ($balance) {
            if ($user->isAdmin() === false) {
                return $balance;
            }
            throw new InvalidUserException('User can\'t create a transaction.');
        }
        throw new InvalidBalanceException('User don\'t have balance and it wasn\'t possible to create one.');
    }

    /**
     * Check if is a image for credit transaction
     */
    private function checkImage(TransactionType $type, ?string $pathImage = null): void
    {
        if ($type == TransactionType::CREDIT && isset($pathImage) === false) {
            throw new InvalidBalanceTransactionException('Credit transaction needs a image to validate a new transaction.');
        }
    }
}
