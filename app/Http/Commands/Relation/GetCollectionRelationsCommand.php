<?php


namespace App\Http\Commands\Relation;


use App\Http\Commands\Abstracts\GetCollectionAbstract;
use App\Http\Commands\Interfaces\GetCollection;
use App\Models\Relation;

class GetCollectionRelationsCommand extends GetCollectionAbstract implements GetCollection
{
    public function filter (): \Illuminate\Database\Eloquent\Builder
    {
        $query = Relation::query();

        if ( isset($this->filters['user']) ) {
            $query->where(function ( $query ) {
                $query->where('applicant_id', $this->filters['user'])
                    ->orWhere('addressee_id', $this->filters['user']);
            });
        }

        if ( isset($this->filters['status']) ) {
            $query->where('status', $this->filters['status']);
        }

        return $query;
    }
}
