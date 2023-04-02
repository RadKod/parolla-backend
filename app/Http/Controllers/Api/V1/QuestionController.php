<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CustomQuestionResource;
use App\Http\Resources\QuestionResource;
use App\Models\CustomQuestion;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends BaseController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $today = Carbon::now();
        $yesterday = $today->copy()->subDay();
        $days_15_ago = $today->copy()->subDays(15);

        $yesterdayCacheKey = 'questions_' . $yesterday->toDateString();
        $todayCacheKey = 'questions_' . $today->toDateString();

        if (Cache::has($todayCacheKey)) {
            $questionIds = Cache::get($todayCacheKey);
            $questions = Question::query()
                ->select('id', 'alphabet_id', 'question', 'answer')
                ->whereIn('id', $questionIds)
                ->with(['alphabet'])
                ->orderBy('alphabet_id')
                ->get();
        } else {
            $excludes = Question::query()
                ->select('id')
                ->whereBetween('release_at', [$days_15_ago, $today])
                ->get()
                ->pluck('id')
                ->toArray();

            $subFromQuery = Question::query()
                ->select('id', 'alphabet_id', 'question', 'answer')
                ->inRandomOrder()->toSql();
            $questions = Question::query()
                ->select('id', 'alphabet_id', 'question', 'answer')
                ->whereNotIn('id', $excludes)
                ->from(DB::raw("($subFromQuery) as sub"))
                ->with(['alphabet'])
                ->groupBy('alphabet_id')
                ->get();

            // get question ids
            $questionIds = $questions->pluck('id')->toArray();
            // update release_at
            Question::query()->whereIn('id', $questionIds)->update(['release_at' => $today]);

//            $questions = Question::query()
//                ->select('id', 'alphabet_id', 'question', 'answer')
//                ->whereNotIn('id', $excludes)
//                ->whereIn('id', Question::query()
//                    ->selectRaw('MIN(id)')->groupBy('alphabet_id')->toBase())
//                ->inRandomOrder()
//                ->orderBy('alphabet_id')
//                ->get();

            Cache::forget($yesterdayCacheKey);
            Cache::flush();
            Cache::forever($todayCacheKey, $questionIds);
        }

        return $this->sendResponse(
            [
                'date' => $today,
                'questions' => QuestionResource::collection($questions),
            ],
            'Questions retrieved successfully.'
        );
    }

    public function unlimited(): JsonResponse
    {
        $subFromQuery = Question::query()
            ->select('id', 'alphabet_id', 'question', 'answer')
            ->inRandomOrder()->toSql();
        $questions = Question::query()
            ->select('id', 'alphabet_id', 'question', 'answer')
            ->from(DB::raw("($subFromQuery) as sub"))
            ->with(['alphabet'])
            ->groupBy('alphabet_id')
            ->orderBy('alphabet_id')
            ->get();

        return $this->sendResponse(
            [
                'questions' => QuestionResource::collection($questions),
            ],
            'Questions retrieved successfully.'
        );
    }

    public function custom_store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'is_public' => 'required|boolean',
            'questions' => 'required|array',
            'questions.*' => 'required|array',
            'questions.*.*' => 'required|string',
            'answers' => 'required|array',
            'answers.*' => 'required|array',
            'answers.*.*' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all());
        }

        $questions = $request->get('questions');
        $answers = $request->get('answers');

        $letter_errors = [];
        foreach ($questions as $key => $question) {
            $answer = $answers[$key];
            $answer_letter = mb_strtolower(mb_substr($answer[0], 0, 1));
            if ($key !== $answer_letter) {
                $letter_errors[] = '\''.$question[0].'\' sorusunun cevabı \''.$key.'\' ile başlamalıdır.';
            }
        }

        if (count($letter_errors) > 0) {
            return $this->sendError('Validation Error.', $letter_errors);
        }

        // crate room hash
        $room = md5(uniqid(mt_rand(), true));
        $title = $request->get('title');
        $is_public = $request->get('is_public');

        foreach ($questions as $key => $question) {
            $answer = $answers[$key];
            $create_question = new CustomQuestion();
            $create_question->title = $title;
            $create_question->is_public = $is_public;
            $create_question->room = $room;
            $create_question->alphabet = $key;
            $create_question->question = $question[0];
            $create_question->answer = $answer[0];
            $create_question->save();
        }

        return $this->sendResponse(
            [
                'room' => $room,
            ],
            'Questions created successfully.'
        );
    }

    public function custom_get(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'room' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all());
        }

        $room = $request->get('room');

        $questions = CustomQuestion::query()
            ->select('id', 'alphabet', 'question', 'answer', 'title', 'is_public')
            ->where('room', $room)
            ->orderBy('alphabet')
            ->get();

        return $this->sendResponse(
            [
                'title' => $questions[0]->title,
                'is_public' => $questions[0]->is_public,
                'questions' => CustomQuestionResource::collection($questions),
            ],
            'Questions retrieved successfully.'
        );
    }
}
