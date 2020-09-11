<?php


namespace App\Http\Commands\Relation;


use App\Relation;
use App\User;

abstract class BaseRelationCommand
{
    protected function getRelationExists ( $query ): bool
    {
        return (bool)$query->count();
    }

    protected function getQueryRelationExists ( User $applicant, User $addressee )
    {
        return Relation::where(static function ($query) use ($applicant) {
            $query->where('applicant_id', $applicant->id)->orWhere('addressee_id', $applicant->id);
        })->where(function ($query) use ($addressee) {
            $query->where('applicant_id', $addressee->id)->orWhere('addressee_id', $this->addressee->id);
        });
    }
}
