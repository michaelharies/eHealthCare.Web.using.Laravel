<?php
/*
 * File name: ClinicUserFactory.php
 * Last modified: 2022.02.02 at 19:13:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */


use App\Models\Clinic;
use App\Models\ClinicUser;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(ClinicUser::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomElement([2, 3, 4]),
        //"user_id" => User::role('clinic_owner')->get()->random()->id,
        'clinic_id' => Clinic::all()->random()->id
    ];
});
