<?php


namespace App\Http\Commands\User;


use App\Http\Commands\Abstracts\GetCollectionAbstract;
use App\Http\Commands\Interfaces\GetCollection;
use App\Http\Resources\UserCollectionResource;
use App\User;

class GetCollectionUsers extends GetCollectionAbstract implements GetCollection
{
    public function getResource (): string
    {
        return UserCollectionResource::class;
    }

    public function filter (): \Illuminate\Database\Eloquent\Builder
    {
        $query = User::query();

        if ( isset($this->params['is_verified']) ) {
            $query->where('is_verified', (bool)$this->params['is_verified']);
        }

        if ( isset($this->params['filter_text']) ) {
            $query->where(function ( $query ) {
                $query->where('name', 'like', '%' .  $this->params['filter_text'] . '%')
                    ->orWhere('email', 'like', '%' .  $this->params['filter_text'] . '%');
            });
        }

        if ( isset($this->request['role']) ) {
            $query->whereHas('roles', function ( $query ) {
                $query->where('name', $this->params['role']);
            });
        }

        return $query;
    }
}
