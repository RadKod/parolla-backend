<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'fingerprint' => $this->fingerprint,
            'room_id' => $this->room_id,
            'game_result' => $this->game_result,
            'score' => $this->score,
            'user' => new UserResource($this->user)
        ];
    }
}
