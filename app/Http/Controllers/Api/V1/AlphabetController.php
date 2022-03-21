<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AlphabetResource;
use App\Models\Alphabet;
use Illuminate\Http\JsonResponse;

class AlphabetController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $alphabet = Alphabet::all();
        return $this->sendResponse(
            AlphabetResource::collection($alphabet), 'Alphabets retrieved successfully.'
        );
    }
}
