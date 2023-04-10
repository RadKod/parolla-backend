<?php

namespace App\Http\Livewire\Room;

use App\Models\CustomQuestion;
use Livewire\Component;

class RoomList extends Component
{
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
            })
            ->get();
        $langs = CustomQuestion::query()
            ->select('lang')
            ->groupBy('lang')
            ->get();
        $langs = $langs->pluck('lang')->toArray();
        $publicRoomCount = 0;
        $privateRoomCount = 0;
        foreach ($customQuestions as $customQuestion) {
            if ($customQuestion->is_public) {
                $publicRoomCount++;
            } else {
                $privateRoomCount++;
            }
        }
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
