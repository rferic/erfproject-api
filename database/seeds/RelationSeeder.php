<?php

use App\Relation;
use App\User;
use Illuminate\Database\Seeder;

class RelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $applicant = User::all()->random();
        $addressees = User::where('id', '<>', $applicant->id)->get()->random(10);

        foreach ( $addressees as $addressee ) {
            factory(Relation::class)->create([
                'applicant_id' => $applicant->id,
                'addressee_id' => $addressee->id
            ]);
        }
    }
}
