<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Relation\RelationResource;
use Illuminate\Http\Request;


/**
 * @method static collection(\Illuminate\Support\Collection $collection)
 */
class UserResource extends UserBaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = parent::toArray($request);

        if ( $request->with ) {
            if ( in_array('applicant_relations', $request->with, true) ) {
                $data['applicant_relations'] = RelationResource::collection($this->applicantRelations()->get());
            }

            if ( in_array('addressee_relations', $request->with, true) ) {
                $data['addressee_relations'] = RelationResource::collection($this->addresseeRelations()->get());
            }

            if ( in_array('blocker_relations', $request->with, true) ) {
                $data['blocker_relations'] = RelationResource::collection($this->blockerRelations());
            }

            if ( in_array('relations', $request->with, true) ) {
                $data['relations'] = RelationResource::collection($this->relations());
            }
        }

        return $data;
    }
}
