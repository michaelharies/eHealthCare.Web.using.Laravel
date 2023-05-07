<?php
/*
 * File name: AwardFactory.php
 * Last modified: 2021.01.17 at 20:46:36
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */


use App\Models\Award;
use App\Models\Clinic;
use App\Models\Coupon;
use App\Models\Doctor;
use App\Models\Speciality;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Coupon::class, function (Faker $faker) {
    $code = ["PROMO10","OFF20","CHRISTMAS","PROMO30","OFF50"];
    $clinics = Clinic::all()->toArray();
    $doctors = Doctor::all()->toArray();
    $specialities = Speciality::all()->toArray();
    return [
        'code' => $faker->unique()->randomElement($code),
        'discount' => $faker->randomElement([5,10,15,20,25]),
        'discount_type' => $faker->randomElement(["percent","fixed"]),
        'description' => $faker->randomElement(["percent","fixed"]),
        'expires_at' => $faker->dateTimeBetween('75 hours','80 hours'),
        'enabled' => $faker->randomElement([0,1]),
//        'clinics' => $faker->randomElements($clinics,2),
//        'doctors' => $faker->randomElements($doctors,2),
//        'specialities' => $faker->randomElements($specialities,2),
    ];
});



