<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CustomQuestionResource;
use App\Http\Resources\CustomQuestionRoomResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\ReviewResource;
use App\Models\CustomQuestion;
use App\Models\Question;
use App\Models\Review;
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
        $days_15_ago = $today->copy()->subDays(16);

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

            if ($questions->count() < 29) {
                $questions = Question::query()
                    ->select('id', 'alphabet_id', 'question', 'answer')
                    ->from(DB::raw("($subFromQuery) as sub"))
                    ->with(['alphabet'])
                    ->groupBy('alphabet_id')
                    ->get();
            }

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
            'room_title' => 'required|string|max:64',
            'is_public' => 'required|boolean',
            'is_anon' => 'boolean',
            'qa_list' => 'required|array',
            'qa_list.*' => 'required|array',
            'qa_list.*.character' => 'required|string|size:1',
            'qa_list.*.question' => 'required|string|max:120',
            'qa_list.*.answer' => 'required|string|max:120',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all());
        }

        $qa_list = $request->get('qa_list');
        $fingerprint = $request->get('fingerprint');
        $is_anon = $request->get('is_anon') ?? false;

        $letter_errors = [];
        foreach ($qa_list as $qa) {
            $answer_letters = explode(',', $qa['answer']);
            foreach ($answer_letters as $answer_letter) {
                # turkish upper replace
                $answer_letter = str_replace(
                    array('İ', 'I', 'Ş', 'Ğ', 'Ü', 'Ö', 'Ç'), array('i', 'ı', 'ş', 'ğ', 'ü', 'ö', 'ç'), $answer_letter
                );
                $character = str_replace(
                    array('İ', 'I', 'Ş', 'Ğ', 'Ü', 'Ö', 'Ç'), array('i', 'ı', 'ş', 'ğ', 'ü', 'ö', 'ç'), $qa['character']
                );

                $answer_letter = mb_strtolower(mb_substr(trim($answer_letter), 0, 1), 'UTF-8');
                if ($answer_letter !== mb_strtolower($character, 'UTF-8')) {
                    $letter_errors[] = '\''.$qa['question'].'\' sorusunu cevabı \''.$qa['character'].'\' ile başlamalı.';
                }
            }
        }

        if (count($letter_errors) > 0) {
            return $this->sendError('Validation Error.', $letter_errors);
        }

        // crate room hash
        $room = md5(uniqid(mt_rand(), true));
        $title = $request->get('room_title');
        $is_public = $request->get('is_public');

        $create_question = new CustomQuestion();
        $create_question->title = $title;
        $create_question->lang = app()->getLocale();
        $create_question->is_public = $is_public;
        $create_question->fingerprint = $fingerprint;
        $create_question->is_anon = $is_anon;
        $create_question->room = $room;
        $create_question->qa_list = $qa_list;
        $create_question->save();

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

        $question = CustomQuestion::query()
            ->select('id', 'qa_list', 'title', 'is_public', 'view_count', 'lang')
            ->where('room', $room)
            ->first();

        if (!$question) {
            return $this->sendError('Validation Error.', ['Oda bulunamadı.']);
        }

        $question->increment('view_count');

        $alphabet = [];
        foreach ($question->qa_list as $qa) {
            $alphabet[] = $qa['character'];
        }

        return $this->sendResponse(
            [
                'title' => $question->title,
                'is_public' => $question->is_public,
                'lang' => $question->lang,
                'view_count' => $question->view_count,
                'question_count' => count($question->qa_list),
                'alphabet' => $alphabet,
                'questions' => CustomQuestionResource::collection($question->qa_list),
            ],
            'Questions retrieved successfully.'
        );
    }

    public function rooms(): JsonResponse
    {
        $rooms = CustomQuestion::query()
            ->select([
                'id', 'room', 'title', 'is_public', 'view_count', 'lang', 'qa_list', 'updated_at',
                'is_anon', 'fingerprint'
            ])
            ->with(['reviews', 'user'])
            ->groupBy('room')
            ->orderBy('updated_at', 'desc')
            ->where('is_public', true)
            ->where('lang', app()->getLocale())
            ->get();

        return $this->sendResponse(
            [
                'rooms' => CustomQuestionRoomResource::collection($rooms),
            ],
            'Rooms retrieved successfully.'
        );
    }

    public function reviews($room_id): JsonResponse
    {
        $reviews = Review::query()
            ->select('id', 'room_id', 'fingerprint', 'content', 'rating', 'created_at')
            ->where('room_id', $room_id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            [
                'reviews' => ReviewResource::collection($reviews),
            ],
            'Reviews retrieved successfully.'
        );
    }

    public function reviews_store(Request $request, $room_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'fingerprint' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all());
        }

        $content = $request->get('content');
        $rating = $request->get('rating');
        $fingerprint = $request->get('fingerprint');

        $review = new Review();
        $review->room_id = $room_id;
        $review->fingerprint = $fingerprint;
        $review->content = $content;
        $review->rating = $rating;
        $review->save();

        return $this->sendResponse(
            [
                'content' => $content,
                'rating' => $rating,
                'fingerprint' => $fingerprint,
            ],
            'Review created successfully.'
        );
    }
}
