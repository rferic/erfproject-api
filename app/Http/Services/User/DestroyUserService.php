<?php


namespace App\Http\Services\User;


use App\User;

class DestroyUserService
{
    private $user;

    public function __construct ( User $user )
    {
        $this->user = $user;
    }

    public function execute (): void
    {
        $this->user->delete();
    }
}
