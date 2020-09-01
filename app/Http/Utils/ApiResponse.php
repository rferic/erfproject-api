<?php


namespace App\Http\Utils;


use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function responseSuccess ( $data, $message = null, Int $code = 200 ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function responseFail ( Array $errors = null, $message = null, Int $code = 500 ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
