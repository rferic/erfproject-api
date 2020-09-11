<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $adminRole = \App\Role::where('name', 'administrator')->first();
        $clientRole = \App\Role::where('name', 'client')->first();

        $user = factory(\App\User::class)->create([
            'email' => config('default.admin.email'),
            'password' => bcrypt(config('default.admin.password'))
        ]);

        $user->attachRole($adminRole);
        $user->attachRole($clientRole);


        factory(\App\User::class, 50)->create([])->each(static function ( $user ) use ( $clientRole ) {
            $user->attachRole($clientRole);
        });
    }
}
