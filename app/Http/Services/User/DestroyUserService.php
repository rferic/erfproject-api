<?php


namespace App\Http\Services\User;


use App\Models\User;

class DestroyUserService
{
    private $user;

    public function __construct ( User $user )
    {
        $this->user = $user;
    }

    public function execute (): User
    {
        $this->user->delete();
        return $this->user;
    }
}
