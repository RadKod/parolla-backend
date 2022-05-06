<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    /**
     * Retrieve index of the questions.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $today = Carbon::now();
        $yesterday = $today->copy()->subDay();

        $yesterdayCacheKey = 'questions_' . $yesterday->toDateString();
        $todayCacheKey = 'questions_' . $today->toDateString();

        if (Cache::has($todayCacheKey)) {
            $questions = Cache::get($todayCacheKey);
        } else {
            $excludes = [];

            if (Cache::has($yesterdayCacheKey)) {
                $oldQuestions = Cache::get($yesterdayCacheKey);
                $excludes = $oldQuestions->pluck('id')->toArray();
            }

            $questions = Question::query()
                ->select('id', 'question', 'answer', 'character')
                ->whereNotIn('id', $excludes)
                ->whereIn('id', Question::query()->selectRaw('MIN(id)')->groupBy('character')->toBase())
                ->inRandomOrder()
                ->get();

            Cache::forever($todayCacheKey, $questions);
            Cache::forget($yesterdayCacheKey);
        }

        return $this->sendResponse(
            [
                'date' => $today->toDateString(),
                'questions' => QuestionResource::collection($questions),
            ],
            'Questions retrieved successfully.'
        );
    }
}
