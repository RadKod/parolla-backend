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
            'id' => $this->id,
            'title' => $this->title,
            'lang' => $this->lang,
            'is_public' => $this->is_public,
            'question_count' => count($this->qa_list),
            'view_count' => (int)$this->view_count,
            'room' => $this->room,
            'review_count' => $this->reviews->count(),
            'rating' => $this->reviews->avg('rating'),
            'user' => !$this->is_anon && $this->user ? new UserResource($this->user) : null,
        ];
    }
}
