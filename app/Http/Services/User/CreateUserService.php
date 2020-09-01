<?php


namespace App\Http\Services\User;


use App\Role;
use App\User;

class CreateUserService
{
    private $data;
    private $roleDefault = 'client';

    public function __construct ( Array $data )
    {
        $this->data = $data;
    }

    public function execute (): User
    {
        $role = Role::where('name', $this->roleDefault)->first();
        $user = User::create($this->data);
        $user->attachRole($role);

        return $user;
    }
}
