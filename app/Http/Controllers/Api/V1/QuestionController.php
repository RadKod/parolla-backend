<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CustomQuestionResource;
use App\Http\Resources\CustomQuestionRoomResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\RoomStatisticResource;
use App\Http\Resources\UserResource;
use App\Models\CustomQuestion;
use App\Models\Question;
use App\Models\Review;
use App\Models\RoomStatistic;
use App\Models\User;
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
                ->whereNotNull('release_at')
                ->pluck('id')
                ->all();

            $subQuery = Question::query()
                ->inRandomOrder()
                ->select('id')
                ->whereNotIn('id', $excludes)
                ->limit(1000);

            $questions = Question::query()
                ->select('questions.id', 'questions.alphabet_id', 'questions.question', 'questions.answer')
                ->joinSub($subQuery, 'sub', function ($join) {
                    $join->on('questions.id', '=', 'sub.id');
                })
                ->with('alphabet')
                ->groupBy('alphabet_id')
                ->get();

            // if not enough questions, get all questions
            if ($questions->count() < 26) {
                $subQuery = Question::query()
                    ->inRandomOrder()
                    ->select('id')
                    ->limit(1000);

                $questions = Question::select('questions.id', 'questions.alphabet_id', 'questions.question', 'questions.answer')
                    ->joinSub($subQuery, 'sub', function ($join) {
                        $join->on('questions.id', '=', 'sub.id');
                    })
                    ->with('alphabet')
                    ->groupBy('alphabet_id')
                    ->get();
            }

            // get question ids
            $questionIds = $questions->pluck('id')->toArray();
            // update release_at
            Question::query()->whereIn('id', $questionIds)->update(['release_at' => $today]);

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
                    $letter_errors[] = '\'' . $qa['question'] . '\' sorusunu cevabı \'' . $qa['character'] . '\' ile başlamalı.';
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
            ->select('id', 'room', 'qa_list', 'title', 'is_public', 'view_count', 'lang')
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
                'id' => $question->id,
                'room' => $question->room,
                'title' => $question->title,
                'is_public' => $question->is_public,
                'lang' => $question->lang,
                'view_count' => $question->view_count,
                'question_count' => count($question->qa_list),
                'review_count' => $question->reviews->count(),
                'rating' => $question->reviews->avg('rating'),
                'user' => !$question->is_anon && $question->user ? new UserResource($question->user) : null,
                'alphabet' => $alphabet,
                'questions' => CustomQuestionResource::collection($question->qa_list),
            ],
            'Questions retrieved successfully.'
        );
    }

    public function rooms(Request $request): JsonResponse
    {
        $per_page = $request->get('per_page') ?? 10;
        $search = $request->get('search') ?? null;
        $lang = $request->get('lang') ?? app()->getLocale();
        $rooms = CustomQuestion::query()
            ->select([
                'id', 'room', 'title', 'is_public', 'view_count', 'lang', 'qa_list', 'updated_at',
                'is_anon', 'fingerprint'
            ])
            ->with(['reviews', 'user'])
            ->groupBy('room')
            ->orderBy('id', 'desc')
            ->where('is_public', true)
            ->where('lang', $lang)
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('room', 'like', '%' . $search . '%');
            })
            ->cursorPaginate($per_page)->withQueryString();


        $total_count = CustomQuestion::query()
            ->select([
                'id', 'room', 'title', 'is_public', 'view_count', 'lang', 'qa_list', 'updated_at',
                'is_anon', 'fingerprint'
            ])
            ->orderBy('id', 'desc')
            ->where('is_public', true)
            ->where('lang', app()->getLocale())
            ->count();

        return $this->sendResponse(
            [
                'rooms' => CustomQuestionRoomResource::collection($rooms),
                'total' => $total_count,
                'pagination' => [
                    'per_page' => $rooms->perPage(),
                    'next_page_url' => $rooms->nextPageUrl(),
                    'prev_page_url' => $rooms->previousPageUrl(),
                ]
            ],
            'Rooms retrieved successfully.'
        );
    }

    public function reviews($room_id): JsonResponse
    {
        $reviews = Review::query()
            ->select('id', 'room_id', 'fingerprint', 'content', 'rating', 'created_at')
            ->where('room_id', $room_id)
            ->with(['user', 'room' => function ($query) {
                $query->select('id', 'room', 'title', 'is_public', 'view_count',
                    'lang', 'updated_at', 'is_anon', 'fingerprint');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            [
                'rating' => (float)number_format($reviews->avg('rating'), 1),
                'review_count' => $reviews->count(),
                'room' => $reviews->first() ? $reviews->first()->room : null,
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

    public function statistics(): array
    {
        $total_public_questions = CustomQuestion::query()
            ->select('id')
            ->where('is_public', true)
            ->count();

        $total_private_questions = CustomQuestion::query()
            ->select('id')
            ->where('is_public', false)
            ->count();

        $total_public_reviews = Review::query()
            ->select('id')
            ->count();

        $total_users = User::query()
            ->select('id')
            ->count();

        $total_room_view_count = CustomQuestion::query()
            ->select('id')
            ->sum('view_count');
        return [
            'total_public_custom_rooms' => $total_public_questions,
            'total_private_custom_rooms' => $total_private_questions,
            'total_custom_rooms' => $total_public_questions + $total_private_questions,
            'total_custom_room_view_count' => number_format($total_room_view_count, 0, ',', '.'),
            'total_reviews' => $total_public_reviews,
            'total_users' => $total_users,
        ];
    }

    public function room_statistics($room_id): JsonResponse
    {
        $room = CustomQuestion::query()
            ->select('id', 'room', 'title', 'is_public', 'view_count', 'lang', 'qa_list', 'updated_at',
                'is_anon', 'fingerprint')
            ->where('id', $room_id)
            ->first();

        if (!$room) {
            return $this->sendError('Validation Error.', ['Oda bulunamadı.'], 404);
        }

        $statistics = RoomStatistic::query()
            ->select('id', 'fingerprint', 'room_id', 'game_result')
            ->where('room_id', $room_id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();


        // {
        //            "id": 4,
        //            "fingerprint": "3229108507",
        //            "room_id": "7306",
        //            "game_result": {
        //                "correctAnswers": [],
        //                "wrongAnswers": [
        //                    {
        //                        "index": 0,
        //                        "letter": "B",
        //                        "isPassed": false,
        //                        "isWrong": true,
        //                        "isCorrect": false
        //                    }
        //                ],
        //                "passedAnswers": [],
        //                "remainTime": {
        //                    "days": 0,
        //                    "hours": 0,
        //                    "minutes": 4,
        //                    "seconds": 54,
        //                    "milliseconds": 995
        //                },
        //                "remainTimeAsMs": 294995
        //            },
        //            "user": {
        //                "username": "gamer9977",
        //                "fingerprint": "3229108507"
        //            }
        //        }

        $statistics = $statistics->map(function ($statistic) {
            $correctCount = count($statistic->game_result['correctAnswers'] ?? []);
            $wrongCount = count($statistic->game_result['wrongAnswers'] ?? []);
            $passedCount = count($statistic->game_result['passedAnswers'] ?? []);
            $timeScore = $statistic->game_result['remainTimeAsMs'] ?? 0; // Assuming higher time is better

            // Define weights
            $weightCorrect = 5;  // High positive impact
            $weightWrong = -2;   // Moderate negative impact
            $weightPassed = -1;  // Low negative impact
            $timeWeight = 0.001; // Small bonus for remaining time

            // Calculate score
            $statistic->score = ($correctCount * $weightCorrect) +
                ($wrongCount * $weightWrong) +
                ($passedCount * $weightPassed) +
                ($timeScore * $timeWeight);

            return $statistic;
        });

        // Sort by the newly calculated score
        $statistics = $statistics->sortByDesc('score');

        return $this->sendResponse(
            RoomStatisticResource::collection($statistics),
            'Room statistics retrieved successfully.'
        );
    }

    public function room_statistics_store(Request $request, $room_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'game_result' => 'required|array',
            'fingerprint' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all());
        }

        $game_result = $request->get('game_result');
        $room = CustomQuestion::query()
            ->select('id', 'room', 'title', 'is_public', 'view_count', 'lang', 'qa_list', 'updated_at',
                'is_anon', 'fingerprint')
            ->where('id', $room_id)
            ->first();

        if (!$room) {
            return $this->sendError('Validation Error.', ['Oda bulunamadı.'], 404);
        }

        $is_played = RoomStatistic::query()
            ->where('room_id', $room_id)
            ->where('fingerprint', $request->get('fingerprint'))
            ->exists();

        if ($is_played) {
            return $this->sendError('Validation Error.', ['Bu oyunu daha önce oynadınız.']);
        }

        $room_statistic = new RoomStatistic();
        $room_statistic->room_id = $room_id;
        $room_statistic->fingerprint = $request->get('fingerprint');
        $room_statistic->game_result = $game_result;
        $room_statistic->save();

        return $this->sendResponse(
            new RoomStatisticResource($room_statistic),
            'Room statistic created successfully.'
        );
    }


}
