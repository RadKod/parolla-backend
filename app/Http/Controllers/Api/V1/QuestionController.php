<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QuestionController extends BaseController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $yesterday = Carbon::now()->subDay()->toDateString();
        $today = Carbon::now()->toDateString();

        $old_cache_file_name = md5('questions_' . $yesterday);
        $cache_file_name = md5('questions_' . $today);

        if (Cache::has($cache_file_name)) {
            $questions = Cache::get($cache_file_name);
        } else {
            $not_in_ids = [];

            if (Cache::has($old_cache_file_name)) {
                $old_questions = Cache::get($old_cache_file_name);
                $not_in_ids = $old_questions->pluck('id')->toArray();
            }

            $subFromQuery = Question::query()
                ->select('id', 'alphabet_id', 'question', 'answer')
                ->inRandomOrder()->toSql();
            $questions = Question::query()
                ->select('id', 'alphabet_id', 'question', 'answer')
                ->whereNotIn('id', $not_in_ids)
                ->from(DB::raw("($subFromQuery) as sub"))
                ->with(['alphabet'])
                ->groupBy('alphabet_id')
                ->get();

            Cache::forever($cache_file_name, $questions);
            Cache::forget($old_cache_file_name);
        }

        return $this->sendResponse(
            [
                'date' => $today,
                'questions' => QuestionResource::collection($questions),
            ],
            'Questions retrieved successfully.'
        );
    }
}
