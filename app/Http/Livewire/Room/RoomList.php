<?php

namespace App\Http\Livewire\Room;

use App\Models\CustomQuestion;
use Livewire\Component;
use Livewire\WithPagination;

class RoomList extends Component
{

    use WithPagination;

    public $selectedRoom = [];
    public $selectedLang = 'all';
    public $roomType = 'all';

    public function render()
    {
        $customQuestions = CustomQuestion::query()
            ->groupBy('room')
            ->orderBy('created_at', 'desc')
            ->when($this->selectedLang !== 'all', function ($query) {
                $query->where('lang', $this->selectedLang);
            })
            ->when($this->roomType !== 'all', function ($query) {
                $query->where('is_public', (bool) $this->roomType);
            })->paginate(10);

        $langs = CustomQuestion::query()
            ->select('lang')
            ->groupBy('lang')
            ->get();
        $langs = $langs->pluck('lang')->toArray();

        $publicRoomCount = CustomQuestion::query()
            ->where('is_public', 1)
            ->count();
        $privateRoomCount = CustomQuestion::query()
            ->where('is_public', 0)
            ->count();
        return view(
            'livewire.room.room-list', compact(
                'customQuestions', 'publicRoomCount', 'privateRoomCount',
                'langs'
            )
        );
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
