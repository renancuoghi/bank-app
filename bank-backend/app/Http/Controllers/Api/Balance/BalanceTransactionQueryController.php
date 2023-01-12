<?php

namespace App\Http\Controllers\Api\Balance;

use App\Domain\Balance\Transaction\Enum\TransactionStatus;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\StorePaginatorDateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BalanceTransactionQueryController extends Controller
{
    private BalanceTransactionRepositoryInterface $balanceTransactionRepository;

    public function __construct(BalanceTransactionRepositoryInterface $balanceTransactionRepository)
    {
        $this->balanceTransactionRepository = $balanceTransactionRepository;
    }

    public function getLastTransactions(StorePaginatorDateRequest $request)
    {
        $data = $this->getDataPage($request);

        $user = $request->user();
        try {
            return $this->sendOkResponse(
                $this->balanceTransactionRepository->getLastTransactions($user->id, TransactionStatus::ACCEPTED, $data["date"], $data["page"], $data["page_size"]),
                null,
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function getPedingTransactions(Request $request)
    {
        $data = $this->getDataPage($request);

        try {
            return $this->sendOkResponse(
                $this->balanceTransactionRepository->getPendingTransactions(null, $data["page"], $data["page_size"]),
                null,
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function getCreditTransactions(StorePaginatorDateRequest $request)
    {
        $data = $this->getDataPage($request);

        $user = $request->user();

        try {
            return $this->sendOkResponse(
                $this->balanceTransactionRepository->getLastCreditTransactions($user->id, $data["status"], $data["date"], $data["page"], $data["page_size"]),
                null,
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function getDebitTransactions(StorePaginatorDateRequest $request)
    {
        $data = $this->getDataPage($request);

        $user = $request->user();
        try {
            return $this->sendOkResponse(
                $this->balanceTransactionRepository->getLastDebitTransactions($user->id, $data["date"], $data["page"], $data["page_size"]),
                null,
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function getTotalIncomingExpenses(StorePaginatorDateRequest $request)
    {
        $data = $this->getDataPage($request);

        $user = $request->user();
        try {
            $responseData = [
                "incoming" => $this->balanceTransactionRepository->getSumAmountByTransactionType($user->id, $data["date"], TransactionType::CREDIT),
                "expenses" => $this->balanceTransactionRepository->getSumAmountByTransactionType($user->id, $data["date"], TransactionType::DEBIT),
            ];

            return $this->sendOkResponse(
                $responseData,
                null,
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    private function getDataPage(Request $request): array
    {
        $data = $request->all();
        if (isset($data["date"])) {
            $date = Carbon::createFromFormat('Y-m-d', $data["date"]);
        } else {
            $date = Carbon::now();
        }


        $page = $data['page'] ?? 1;
        $pageSize = $data['page_size'] ?? 10;

        $transactionStatus = TransactionStatus::ACCEPTED;
        if (isset($data["status"])) {
            $transactionStatus = TransactionStatus::from($data["status"]);
        }

        return [
            'date' => $date,
            'page' => $page,
            'page_size' => $pageSize,
            'status' => $transactionStatus,
        ];
    }
}
