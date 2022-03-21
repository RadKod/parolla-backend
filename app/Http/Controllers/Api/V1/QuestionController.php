<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;

class QuestionController extends BaseController
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|JsonResponse
     */
    public function index()
    {

        $questions = Question::query()
            ->select('id', 'alphabet_id', 'question', 'answer')
            ->with(['alphabet'])
            ->orderBy('alphabet_id')->inRandomOrder()
            ->toSql();
        dd($questions);
        return $questions;
        $questions = Question::query()->with('alphabet')
            ->whereNotIn('id', [])
            ->groupBy('alphabet_id')
            ->orderBy('alphabet_id')->inRandomOrder()->get()
            ->mapToGroups(function ($item) {
                return [$item->alphabet_id => $item];
            });

        return $this->sendResponse(
            [
                'date' => date('Y-m-d'),
                'questions' => QuestionResource::collection($questions),
            ],
            'Questions retrieved successfully.'
        );
    }
}
