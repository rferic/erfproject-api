<?php

namespace App\Http\Resources\Relation;

use App\Http\Resources\PaginationAbstract;
use Illuminate\Http\Request;

class RelationCollectionResource extends PaginationAbstract
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
            'collection' => RelationResource::collection($this->collection),
            'pagination' => $this->getPaginationData()
        ];
    }
}
