<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\CustomQuestion;
use App\Models\Review;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public $labels = ["Public Custom Rooms", "Private Custom Rooms", "Public Reviews", "Total Users", "Total Room View Count"];
    public $last_30_days_user_counts = [];
    public $counts = [30, 36, 42, 78, 8899];

    public function render()
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

        // last of 30 days user count gruop by date
        $this->last_30_days_user_counts = User::query()
            ->selectRaw('DATE(created_at) as date, count(id) as count')
            ->whereRaw('DATE(created_at) > DATE_SUB(CURDATE(), INTERVAL 30 DAY)')
            ->groupBy('date')
            ->get();

        $this->counts = [$total_public_questions, $total_private_questions, $total_public_reviews, $total_users, intval($total_room_view_count)];

        return view('livewire.dashboard.dashboard');
    }
}
