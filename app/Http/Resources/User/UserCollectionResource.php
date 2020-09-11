<?php

namespace App\Http\Resources\User;

use App\Http\Resources\PaginationAbstract;
use Illuminate\Http\Request;

class UserCollectionResource extends PaginationAbstract
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
            'collection' => UserResource::collection($this->collection),
            'pagination' => $this->getPaginationData()
        ];
    }
}
