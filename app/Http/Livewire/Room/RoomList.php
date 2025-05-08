<?php

namespace App\Http\Livewire\Room;

use App\Models\CustomQuestion;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class RoomList extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $selectedRoom = [];
    public $selectedLang = 'all';
    public $roomType = 'all';
    public $searchTerm = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'selectedLang' => ['except' => 'all'],
        'roomType' => ['except' => 'all'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(): void
    {
        $this->selectedLang = request()->query('selectedLang', $this->selectedLang);
        $this->roomType = request()->query('roomType', $this->roomType);
    }

    public function render()
    {
        $customQuestions = CustomQuestion::query()
            ->groupBy('room')
            ->when($this->selectedLang !== 'all', function ($query) {
                $query->where('lang', $this->selectedLang);
            })
            ->when($this->roomType !== 'all', function ($query) {
                $query->where('is_public', (bool) $this->roomType);
            })
            ->when($this->searchTerm, function ($query) {
                $query->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('room', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereJsonContains('qa_list', [
                        'question' => $this->searchTerm
                    ])
                    ->orWhereJsonContains('qa_list', [
                        'answer' => $this->searchTerm
                    ]);
            });
            
        // Apply sorting
        switch ($this->sortField) {
            case 'reviews_avg':
                // Kullanım metriğini hesapla: rating'in ağırlığı ve review sayısını birleştirerek
                // Daha güvenilir bir sıralama sağlayacak bayesian ortalama benzeri formül
                $customQuestions = $customQuestions
                    ->withCount('reviews')
                    ->withAvg('reviews', 'rating')
                    ->orderByRaw('(IFNULL(reviews_avg_rating, 0) * 
                                  LEAST(reviews_count, 20) / 20 + 
                                  (3 * (1 - LEAST(reviews_count, 20) / 20))) DESC');
                break;
            case 'view_count':
                $customQuestions = $customQuestions->orderBy('view_count', $this->sortDirection);
                break;
            case 'question_count':
                // Use a raw query to count the length of the JSON array
                $customQuestions = $customQuestions->orderByRaw('JSON_LENGTH(qa_list) ' . $this->sortDirection);
                break;
            default:
                $customQuestions = $customQuestions->orderBy($this->sortField, $this->sortDirection);
                break;
        }
            
        $customQuestions = $customQuestions->paginate(12);

        $langs = CustomQuestion::query()
            ->select('lang')
            ->groupBy('lang')
            ->get();
        $langs = $langs->pluck('lang')->toArray();

        $publicRoomCount = CustomQuestion::query()
            ->where('is_public', 1)
            ->when($this->selectedLang !== 'all', function ($query) {
                $query->where('lang', $this->selectedLang);
            })
            ->count();
        $privateRoomCount = CustomQuestion::query()
            ->when($this->selectedLang !== 'all', function ($query) {
                $query->where('lang', $this->selectedLang);
            })
            ->where('is_public', 0)
            ->count();
        return view(
            'livewire.room.room-list', compact(
                'customQuestions', 'publicRoomCount', 'privateRoomCount',
                'langs'
            )
        );
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function selectRoom($room)
    {
        $this->selectedRoom = CustomQuestion::query()
            ->where('room', $room)
            ->first();
    }

    public function closeRoom()
    {
        $this->selectedRoom = [];
    }

    public function deleteRoom($room)
    {
        CustomQuestion::query()
            ->where('room', $room)
            ->delete();
        $this->selectedRoom = [];
    }

    public function changeRoomType($room)
    {
        $customQuestion = CustomQuestion::query()
            ->where('room', $room)
            ->first();
        $customQuestion->is_public = !$customQuestion->is_public;
        $customQuestion->save();
    }
}
