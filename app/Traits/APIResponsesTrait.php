<?php

namespace App\Traits;

trait APIResponsesTrait
{
    public function successResponse(int $code = 200, $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $code);
    }

    public function errorResponse(int $code = 400, $message = 'Error')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
