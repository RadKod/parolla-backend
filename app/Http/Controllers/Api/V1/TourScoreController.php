<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\API\V1\BaseController;
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
}
