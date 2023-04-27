<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'fingerprint' => $this->fingerprint,
            'room_id' => $this->room_id,
            'content' => $this->content,
            'rating' => $this->rating,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at,
        ];
    }
}
