<?php

namespace App\Http\Controllers\Api\Balance;

use App\Domain\Balance\Balance\Service\BalanceServiceInterface;
use App\Domain\Balance\Transaction\Enum\TransactionType;
use App\Domain\Balance\Transaction\Repository\BalanceTransactionRepository;
use App\Helper\File\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Balance\Transaction\StoreTransactionCreateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BalanceTransactionController extends Controller
{
    private BalanceServiceInterface $balanceService;

    private BalanceTransactionRepository $balanceTransactionRepository;

    public function __construct(BalanceServiceInterface $balanceService, BalanceTransactionRepository $balanceTransactionRepository)
    {
        $this->balanceService = $balanceService;
        $this->balanceTransactionRepository = $balanceTransactionRepository;
    }

    public function store(StoreTransactionCreateRequest $request)
    {
        $data = $request->all();
        $transactionType = TransactionType::from($data['transaction_type']);
        $user = $request->user();
        $pathImage = null;

        $created_at = Carbon::now();
        if (isset($data['created_at'])) {
            $created_at = Carbon::createFromFormat('Y-m-d', $data["created_at"]);
        }

        try {
            // if it is a credit transaction we'll need a image, first store a image to pass the url_path

            if ($transactionType == TransactionType::CREDIT && isset($data['image'])) {
                $prefix = 'u'.$user->id . '_'. date('Y-m-d') . '_';
                $pathImage = ImageHelper::saveImage($data['image'], null, $prefix);
            }

            $transaction = $this->balanceService->createTransaction($user, $data['amount'], $data['description'], $transactionType, $pathImage, $created_at);

            return $this->sendOkResponse($transaction, 'Balance Transaction created In Successfully', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            // if there was an error delete the image if it exists
            if (isset($pathImage)) {
                ImageHelper::deleteImage($pathImage);
            }
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function accept($transactionId, Request $request)
    {
        try {
            if ($this->balanceService->acceptTransaction($transactionId, $request->user())) {
                return $this->sendOkResponse($transactionId, 'Balance Transaction accepted');
            }

            return $this->sendErrorResponse("There was an error while accepting");
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function reject($transactionId, Request $request)
    {
        try {
            if ($this->balanceService->rejectTransaction($transactionId, $request->user())) {
                return $this->sendOkResponse($transactionId, 'Balance Transaction rejected');
            }

            return $this->sendErrorResponse("There was an error while rejecting");
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function getTransaction($transactionId, Request $request)
    {
        $user = $request->user();
        $transaction = $this->balanceTransactionRepository->getTransactionById($transactionId);
        if (isset($transaction) === true) {
            if ($user->is_admin == true || ($user->id == $transaction->user_id)) {
                return $this->sendOkResponse($transaction, '');
            }
            return $this->sendErrorResponse('Access denied', null, Response::HTTP_FORBIDDEN);
        } else {
            return $this->sendErrorResponse('Not Found', null, Response::HTTP_NOT_FOUND);
        }
        // if($user->is_admin==true ||)
    }
}
