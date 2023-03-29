<?php

namespace App\Http\Livewire\Room;

use App\Models\CustomQuestion;
use Livewire\Component;

class RoomList extends Component
{
    public $selectedRoom = [];

    public function render()
    {
        // CustomQuestion group by room
        $customQuestions = CustomQuestion::query()
            ->groupBy('room')
            ->get();
        return view('livewire.room.room-list', compact('customQuestions'));
    }

    public function selectRoom($room)
    {
        $this->selectedRoom = CustomQuestion::query()
            ->where('room', $room)
            ->get();
    }

    public function closeRoom()
    {
        $this->selectedRoom = [];
    }
}
