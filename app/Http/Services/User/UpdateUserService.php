<?php


namespace App\Http\Services\User;


use App\Models\User;

class UpdateUserService
{
    private $user;
    private $data;

    public function __construct ( User $user, Array $data )
    {
        $this->user = $user;
        $this->data = $data;
    }

    public function execute (): User
    {
        $this->user->fill($this->data);
        $this->user->save();

        return $this->user;
    }
}
