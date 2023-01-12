<?php

namespace App\Http\Controllers\Api\User;

use App\Domain\Balance\Balance\Repository\BalanceRepositoryInterface;
use App\Domain\User\Model\User;
use App\Domain\User\Service\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreLoginRequest;
use App\Http\Requests\User\StoreUserRegisterRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserServiceInterface $userService;

    private BalanceRepositoryInterface $balanceRepository;

    public function __construct(UserServiceInterface $userService, BalanceRepositoryInterface $balanceRepository)
    {
        $this->userService = $userService;
        $this->balanceRepository = $balanceRepository;
    }

    /**
     * User login
     *
     * @param StoreLoginRequest $request
     * @return User
     */
    public function login(StoreLoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only(['username', 'password']))) {
                return $this->sendErrorResponse('Username & Password does not match with our record.', null, Response::HTTP_UNAUTHORIZED);
            }
            $token = $this->userService->createToken($request->username);
            $user = $this->userService->getByUsername($request->username);
            $reponseData = ['token' => $token, 'user' => $user];

            return $this->sendOkResponse($reponseData, 'User Logged In Successfully');
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }


    public function create(StoreUserRegisterRequest $request)
    {
        $data = $request->all();
        $data['is_admin'] = false;

        try {
            $user = $this->userService->create($data);
            return $this->sendOkResponse($user, 'User created In Successfully', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function getBalance(Request $request)
    {
        try {
            $user = $request->user();
            return $this->sendOkResponse(
                $this->balanceRepository->createOrGetByUserId($user->id),
                null,
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->sendErrorResponse($th->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendOkResponse(null, "Logout successful", Response::HTTP_OK);
    }
}
