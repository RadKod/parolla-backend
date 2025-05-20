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
    public function byFingerprint(Request $request): JsonResponse
    {
        $fingerprint = $request->query('fingerprint');
        if (!$fingerprint) {
            return $this->sendError('Fingerprint is required', [], 400);
        }

        $user = User::where('fingerprint', $fingerprint)->first();
        if (!$user) {
            return $this->sendError('User not found', [], 404);
        }

        $now = Carbon::now();

        // Günlük başlangıcı ve bitişi
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();

        // Haftalık başlangıcı ve bitişi (Pazartesi - Pazar, Carbon varsayılanı)
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        // Aylık başlangıcı ve bitişi
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Günlük skor
        $daily = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfDay, $endOfDay])
            ->sum('score');

        // Haftalık skor
        $weekly = TourScore::where('user_id', $user->id)
            ->whereBetween('score_date', [$startOfWeek, $endOfWeek])
            ->sum('score');

        // Aylık skor
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
        ], 'User and tour scores retrieved successfully');
    }
}
