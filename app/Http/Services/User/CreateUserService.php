<?php


namespace App\Http\Services\User;


use App\Models\Role;
use App\Models\User;

class CreateUserService
{
    private $name;
    private $email;
    private $password;
    private $roleDefault = 'client';

    public function __construct ( Array $data )
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
    }

    public function execute (): User
    {
        $role = Role::where('name', $this->roleDefault)->first();
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'email_verified_at' => now()
        ]);
        $user->attachRole($role);

        return $user;
    }
}
