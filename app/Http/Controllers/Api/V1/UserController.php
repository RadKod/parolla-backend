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

        // Skorlar
        $daily = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfDay, $endOfDay])
            ->sum('score');

        $weekly = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfWeek, $endOfWeek])
            ->sum('score');

        $monthly = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfMonth, $endOfMonth])
            ->sum('score');

        return $this->sendResponse([
            'user' => $user,
            'tourScores' => [
                'daily' => $daily,
                'weekly' => $weekly,
                'monthly' => $monthly,
            ],
        ], 'Kullanıcı ve tur skorları başarıyla getirildi.');
    }
}
