<?php

namespace App\Http\Resources\Relation;

use App\Http\Resources\User\UserMinifyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed status
 * @property mixed applicant
 * @property mixed addressee
 * @property mixed blocker
 */
class RelationResource extends JsonResource
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
            'status' => $this->status,
            'applicant' => New UserMinifyResource($this->applicant),
            'addressee' => New UserMinifyResource($this->addressee),
            'blocker' => New UserMinifyResource($this->blocker)
        ];
    }
}
