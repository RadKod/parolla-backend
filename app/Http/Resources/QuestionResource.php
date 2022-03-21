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
     */
    public function toArray($request): array
    {
        $data = $this[0];
        return [
            'id' => $data->id,
            'question' => $data->question,
            'letter' => $data->alphabet->name,
            'answer' => $data->answer,
        ];
    }
}
