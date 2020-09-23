<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Role::class)->create([
            'name' => 'root',
            'display_name' => 'Root',
            'description' => 'Root role'
        ]);

        factory(\App\Models\Role::class)->create([
            'name' => 'client',
            'display_name' => 'Client',
            'description' => 'Client role'
        ]);
    }
}
