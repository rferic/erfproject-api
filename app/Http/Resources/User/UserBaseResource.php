<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed avatar
 * @property mixed email
 * @property mixed is_verified
 * @property mixed name
 * @property mixed roles
 * @property mixed social_data
 */
class UserBaseResource extends JsonResource
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
            'is_verified' => $this->is_verified,
            'name' => $this->name,
            'roles' => $this->roles->pluck('name'),
            'social_data' => $this->social_data
        ];
    }
}
