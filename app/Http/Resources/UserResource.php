<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed avatar
 * @property mixed email
 * @property mixed is_verified
 * @property mixed name
 * @property mixed roles
 * @property mixed social_data
 */
class UserResource extends JsonResource
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
            'avatar' => $this->avatar,
            'email' => $this->email,
            'is_verified' => $this->is_verified,
            'name' => $this->name,
            'roles' => $this->roles->pluck('name'),
            'social_data' => $this->social_data
        ];
    }
}
