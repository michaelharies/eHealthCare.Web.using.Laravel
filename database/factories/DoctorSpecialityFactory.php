<?php
/*
 * File name: DoctorCategoyFactory.php
 * Last modified: 2021.03.02 at 14:35:34
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */


use App\Models\Speciality;
use App\Models\Clinic;
use App\Models\DoctorSpeciality;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(DoctorSpeciality::class, function (Faker $faker) {
    return [
        'speciality_id' => Speciality::all()->random()->id,
        'doctor_id' => Clinic::all()->random()->id
    ];
});
