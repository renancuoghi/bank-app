<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;


    public function sendOkResponse($data, string $message = null, $statusCode =  Response::HTTP_OK)
    {
        return response()->json([
            'status' => true,
            'message' =>  $message,
            'data' => $data
        ], $statusCode);
    }

    public function sendErrorResponse($errorMessage, $errorData = [], $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            'status' => false,
            'error' => $errorMessage,
            'data' => $errorData
        ], $statusCode);
    }
}
