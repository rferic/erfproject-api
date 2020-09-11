<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Relation;
use App\User;
use Faker\Generator as Faker;

$factory->define(Relation::class, static function (Faker $faker) {
    $applicant = User::all()->random();
    $requested = User::where('id', '<>', $applicant->id)->get()->random();
    $status = Relation::$TYPES[$faker->randomKey(Relation::$TYPES)];

    return [
        'applicant_id' => $applicant->id,
        'addressee_id' => $requested->id,
        'blocker_id' => $status === 'hate' ? $requested->id : null,
        'status' => $status
    ];
});
