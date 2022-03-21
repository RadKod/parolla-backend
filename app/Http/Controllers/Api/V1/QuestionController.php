<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;

class QuestionController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $questions = Question::query()->with('alphabet')
            ->whereNotIn('id', [])
            ->orderBy('alphabet_id')->inRandomOrder()->get()
            ->mapToGroups(function ($item) {
                return [$item->alphabet_id => $item];
            });
        return $this->sendResponse(
            [
                'date' => date('Y-m-d H:i:s'),
                'questions' => QuestionResource::collection($questions),
            ],
            'Questions retrieved successfully.'
        );
    }
}
