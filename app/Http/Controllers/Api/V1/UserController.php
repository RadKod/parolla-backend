<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\User;
use App\Models\TourScore;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class UserController extends BaseController
{
    /**
     * Kullanıcıyı id veya fingerprint ile getirir ve skorlarını döndürür
     *
     * GET /api/v1/user?id=1  veya  GET /api/v1/user?fingerprint=abc
     */
    public function byIdOrFingerprint(Request $request): JsonResponse
    {
        $id = $request->query('id');
        $fingerprint = $request->query('fingerprint');

        if (!$id && !$fingerprint) {
            return $this->sendError('id veya fingerprint parametresi zorunludur.', [], 400);
        }

        $query = User::query();
        if ($id) {
            $user = $query->where('id', $id)->first();
        } else {
            $user = $query->where('fingerprint', $fingerprint)->first();
        }

        if (!$user) {
            return $this->sendError('Kullanıcı bulunamadı.', [], 404);
        }

        $now = Carbon::now();

        // Tarih aralıkları
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Skor ve rank hesaplamaları
        $dailyScore = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfDay, $endOfDay])
            ->sum('score');
        $dailyRank = $this->getUserRankForPeriod($user->id, $startOfDay, $endOfDay);

        $weeklyScore = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfWeek, $endOfWeek])
            ->sum('score');
        $weeklyRank = $this->getUserRankForPeriod($user->id, $startOfWeek, $endOfWeek);

        $monthlyScore = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfMonth, $endOfMonth])
            ->sum('score');
        $monthlyRank = $this->getUserRankForPeriod($user->id, $startOfMonth, $endOfMonth);

        $allTimeScore = TourScore::where('user_id', $user->id)
            ->sum('score');
        $allTimeRank = $this->getUserRankForPeriod($user->id, null, null);

        $userArray = $user->toArray();
        $userArray['tourScores'] = [
            'daily' => ['score' => $dailyScore, 'rank' => $dailyRank],
            'weekly' => ['score' => $weeklyScore, 'rank' => $weeklyRank],
            'monthly' => ['score' => $monthlyScore, 'rank' => $monthlyRank],
            'allTime' => ['score' => $allTimeScore, 'rank' => $allTimeRank],
        ];

        return $this->sendResponse([
            'user' => $userArray
        ], 'Kullanıcı ve tur skorları başarıyla getirildi.');
    }

    /**
     * Belirli bir zaman aralığı için kullanıcının rank bilgisini döndürür.
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    private function getUserRankForPeriod($userId, $startDate, $endDate)
    {
        // Leaderboard skor listesini topla
        $query = TourScore::select('user_id')
            ->selectRaw('SUM(score) as total_score')
            ->groupBy('user_id')
            ->orderByDesc('total_score');

        // Eğer tarih aralığı belirtilmişse filtrele
        if ($startDate && $endDate) {
            $query->whereBetween('score_date', [$startDate, $endDate]);
        }

        $scoreList = $query->get();

        $rank = 1;
        foreach ($scoreList as $item) {
            if ($item->user_id == $userId) {
                return $rank;
            }
            $rank++;
        }
        // Kullanıcı o periyotta hiç skor yapmamışsa 0 döndür
        return 0;
    }
}
