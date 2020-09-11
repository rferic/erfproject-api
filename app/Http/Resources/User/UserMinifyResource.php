<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed avatar
 * @property mixed email
 * @property mixed name
 */
class UserMinifyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
