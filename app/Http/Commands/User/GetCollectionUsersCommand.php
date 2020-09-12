<?php


namespace App\Http\Commands\User;


use App\Http\Commands\Abstracts\GetCollectionAbstract;
use App\Http\Commands\Interfaces\GetCollection;
use App\User;

class GetCollectionUsersCommand extends GetCollectionAbstract implements GetCollection
{
    public function filter (): \Illuminate\Database\Eloquent\Builder
    {
        $query = User::query();

        if ( isset($this->filters['is_verified']) ) {
            if ( $this->filters['is_verified'] ) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if ( isset($this->filters['filter_text']) ) {
            $query->where(function ( $query ) {
                $query->where('name', 'like', '%' .  $this->filters['filter_text'] . '%')
                    ->orWhere('email', 'like', '%' .  $this->filters['filter_text'] . '%');
            });
        }

        if ( isset($this->request['role']) ) {
            $query->whereHas('roles', function ( $query ) {
                $query->where('name', $this->filters['role']);
            });
        }

        return $query;
    }
}
