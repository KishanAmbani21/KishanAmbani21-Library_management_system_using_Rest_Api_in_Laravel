<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    /**
     * successResponse
     *
     * @param  mixed $data
     * @param  mixed $messageKey
     * @param  mixed $statusCode
     * @return JsonResponse
     */
    public function successResponse($data, $messageKey = 'success', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => trans($messageKey),
            'message_code' => $messageKey,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * errorResponse
     *
     * @param  mixed $messageKey
     * @param  mixed $statusCode
     * @param  mixed $errorDetails
     * @return JsonResponse
     */
    public function errorResponse($messageKey = 'error', $statusCode = 500, $errorDetails = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => trans($messageKey),
            'message_code' => $messageKey,
            'errors' => $errorDetails,
        ], $statusCode);
    }
}
