<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Returns a success json response.
     *
     * @param $result
     * @param $message
     * @return JsonResponse
     */
    public function sendResponse($result, $message): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response);
    }

    /**
     * Returns an error json response.
     *
     * @param $error
     * @param array $error_messages
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($error, array $error_messages = [], int $code = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($error_messages)) {
            $response['data'] = $error_messages;
        }

        return response()->json($response, $code);
    }
}
