<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'fingerprint' => $this->fingerprint,
            # 'email' => $this->when($this->is_permanent, $this->email),
            'is_permanent' => $this->is_permanent,
        ];
    }
}
