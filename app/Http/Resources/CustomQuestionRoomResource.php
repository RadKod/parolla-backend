<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomQuestionRoomResource extends JsonResource
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
            'title' => $this->title,
            'lang' => $this->lang,
            'is_public' => $this->is_public,
            'view_count' => (int)$this->view_count,
            'room' => $this->room,
        ];
    }
}
