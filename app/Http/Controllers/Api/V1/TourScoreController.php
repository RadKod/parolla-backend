<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\TourScore;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TourScoreController extends BaseController
{
    /**
     * Kullanıcının tur puanlarını getir
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'date|date_format:Y-m-d',
            'end_date' => 'date|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray());
        }
        
        $query = TourScore::where('user_id', $user->id);
        
        // Tarihe göre filtreleme
        if ($request->has('start_date')) {
            $query->where('score_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->where('score_date', '<=', $request->end_date);
        }
        
        $scores = $query->orderBy('score_date', 'desc')->get();
        
        return $this->sendResponse([
            'scores' => $scores,
            'total_score' => $scores->sum('score'),
            'average_score' => $scores->count() > 0 ? round($scores->avg('score'), 2) : 0,
        ], __('messages.tour_scores_retrieved'));
    }

    /**
     * Kullanıcının tur puanını kaydet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        
        $validator = Validator::make($request->all(), [
            'score' => 'required|integer|min:0',
            'score_date' => 'date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray());
        }
        
        // Tarih belirtilmemişse bugünün tarihini kullan
        $scoreDate = $request->has('score_date') 
            ? Carbon::parse($request->score_date) 
            : Carbon::today();
        
        // Aynı tarihte kayıt varsa güncelle, yoksa yeni kayıt oluştur
        $tourScore = TourScore::updateOrCreate(
            [
                'user_id' => $user->id,
                'score_date' => $scoreDate,
            ],
            [
                'score' => $request->score,
            ]
        );
        
        return $this->sendResponse(
            $tourScore, 
            __('messages.tour_score_saved')
        );
    }

    /**
     * Liderlik tablosunu getir (günlük, haftalık, aylık)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'integer|min:1|max:100',
            'type' => 'string|in:daily,weekly,monthly,all',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray());
        }

        $limit = $request->input('limit', 10);
        $type = $request->input('type', 'all');
        
        $now = Carbon::now();
        
        // Farklı zaman dilimleri için leaderboard hazırla
        $leaderboards = [];
        
        // Günlük liderlik tablosu
        if ($type === 'daily' || $type === 'all') {
            $leaderboards['daily'] = $this->getLeaderboardForPeriod(
                $now->copy()->startOfDay(),
                $now,
                $limit
            );
        }
        
        // Haftalık liderlik tablosu
        if ($type === 'weekly' || $type === 'all') {
            $leaderboards['weekly'] = $this->getLeaderboardForPeriod(
                $now->copy()->startOfWeek(),
                $now,
                $limit
            );
        }
        
        // Aylık liderlik tablosu
        if ($type === 'monthly' || $type === 'all') {
            $leaderboards['monthly'] = $this->getLeaderboardForPeriod(
                $now->copy()->startOfMonth(),
                $now,
                $limit
            );
        }
        
        return $this->sendResponse(
            $leaderboards,
            __('messages.leaderboard_retrieved')
        );
    }

    /**
     * Belirli bir zaman aralığı için liderlik tablosu getir
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $limit
     * @return array
     */
    private function getLeaderboardForPeriod(Carbon $startDate, Carbon $endDate, int $limit): array
    {
        $users = TourScore::with('user:id,username')
            ->select('user_id')
            ->selectRaw('SUM(score) as total_score')
            ->whereBetween('score_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'username' => $item->user->username,
                    'score' => $item->total_score,
                ];
            })
            ->toArray();
            
        return $users;
    }
}
