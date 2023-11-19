<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;

class Helper
{
    public function apiResponse($status, $data, $message): JsonResponse
    {
        return response()->json([
            'success' => $status,
            'data' => $data,
            'message' => $message,
        ]);
    }
}
