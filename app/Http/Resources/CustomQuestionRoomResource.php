<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomQuestionRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Eksi'den sıfıra level atlatan o sorular
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'is_public' => $this->is_public,
            'view_count' => (int)$this->view_count,
            'room' => $this->room,
        ];
    }
}
