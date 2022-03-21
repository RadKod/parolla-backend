<?php

namespace App\Http\Controllers\API\V1;


use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;


class BaseController extends Controller
{
    /**
     * success response method.
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

        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @param $error
     * @param array $error_messages
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($error, array $error_messages = [], int $code = 200): JsonResponse
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
