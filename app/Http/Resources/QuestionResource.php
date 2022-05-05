<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     * @noinspection PhpUndefinedFieldInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'letter' => $this->character,
            'answer' => $this->answer,
        ];
    }
}
