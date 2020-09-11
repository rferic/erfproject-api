<?php


namespace App\Http\Commands\Relation;


use App\Http\Commands\Abstracts\GetCollectionAbstract;
use App\Http\Commands\Interfaces\GetCollection;
use App\Relation;

class GetCollectionRelationsCommand extends GetCollectionAbstract implements GetCollection
{
    public function filter (): \Illuminate\Database\Eloquent\Builder
    {
        $query = Relation::query();

        if ( isset($this->filters['status']) ) {
            $query->where('status', $this->filters['status']);
        }

        return $query;
    }
}
