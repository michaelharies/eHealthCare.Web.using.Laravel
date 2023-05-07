<?php
/*
 * File name: ClinicReviewFactory.php
 * Last modified: 2021.02.04 at 18:49:42
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */


use App\Models\Clinic;
use App\Models\ClinicReview;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(ClinicReview::class, function (Faker $faker) {
    return [
        "review" => $faker->realText(100),
        "rate" => $faker->numberBetween(1, 5),
        "user_id" => User::role('customer')->get()->random()->id,
        "clinic_id" => Clinic::all()->random()->id,
    ];
});
