<?php

namespace App\Http\Resources\LinkedSocialAccount;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkedSocialAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray ( $request ): array
    {
        return [
            $this->provider_name => $this->provider_data
        ];
    }
}
