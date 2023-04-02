<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CustomQuestionResource;
use App\Http\Resources\CustomQuestionRoomResource;
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
            'room_title' => 'required|string',
            'is_public' => 'required|boolean',
            'qa_list' => 'required|array',
            'qa_list.*' => 'required|array',
            'qa_list.*.character' => 'required|string',
            'qa_list.*.question' => 'required|string',
            'qa_list.*.answer' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all());
        }

        $qa_list = $request->get('qa_list');

        $letter_errors = [];
        foreach ($qa_list as $qa) {
            $answer_letter = mb_strtolower(mb_substr($qa['answer'][0], 0, 1));
            if ($qa['character'] !== $answer_letter) {
                $letter_errors[] = '\''.$qa['question'][0].'\' sorusunun cevabı \''.$qa['character'].'\' ile başlamalıdır.';
            }
        }

        if (count($letter_errors) > 0) {
            return $this->sendError('Validation Error.', $letter_errors);
        }

        // crate room hash
        $room = md5(uniqid(mt_rand(), true));
        $title = $request->get('room_title');
        $is_public = $request->get('is_public');

        foreach ($qa_list as $qa) {
            $create_question = new CustomQuestion();
            $create_question->title = $title;
            $create_question->is_public = $is_public;
            $create_question->room = $room;
            $create_question->alphabet = $qa['character'];
            $create_question->question = $qa['question'];
            $create_question->answer = $qa['answer'];
            $create_question->save();
        }

        return $this->sendResponse(
            [
                'title' => $title,
                'room' => $room
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

    public function rooms(): JsonResponse
    {
        $rooms = CustomQuestion::query()
            ->select('room', 'title', 'is_public')
            ->groupBy('room')
            ->orderBy('id', 'desc')
            ->where('is_public', true)
            ->get();

        return $this->sendResponse(
            [
                'rooms' => CustomQuestionRoomResource::collection($rooms),
            ],
            'Rooms retrieved successfully.'
        );
    }
}
