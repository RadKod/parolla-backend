<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AlphabetResource;
use App\Models\Character;
use Illuminate\Http\JsonResponse;

class AlphabetController extends Controller
{
    /**
     * Retrieve index of the alphabet.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $alphabet = Character::all();

        return $this->sendResponse(
            AlphabetResource::collection($alphabet), 'Alphabets retrieved successfully.'
        );
    }
}
