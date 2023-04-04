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
            ->orderBy('updated_at', 'desc')
            ->get();
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
            'livewire.room.room-list', compact('customQuestions', 'publicRoomCount', 'privateRoomCount')
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
