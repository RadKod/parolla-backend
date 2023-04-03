<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'question' => $this->resource['question'],
            'letter' => $this->resource['character'],
            'answer' => $this->resource['answer'],
        ];
    }
}
