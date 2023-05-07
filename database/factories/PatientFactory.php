<?php
/*
 * File name: PatientFactory.php
 * Last modified: 2021.08.04 at 18:10:26
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */


use App\Models\Patient;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Patient::class, function (Faker $faker) {
    return [
        'first_name' =>  $faker->randomElement(
            ["Patty","Constance","Rita","Anne","Polly","Ester","Ivan","Laura","Coral","Ray","Polly","Wayde","Mark","Penny"]) . " ",
        'last_name' => $faker->randomElement(
            ["O’Furniture","Noring","Book","Teak","Pipe","La Vista","Itchinos","Norda","Trout","Manta","Norma","Polly","Leeva","Waites"]),
        'user_id' => User::role('customer')->get()->random()->id,
        'phone_number' => $faker->phoneNumber,
        'mobile_number' => $faker->phoneNumber,
        'age' => $faker->numberBetween(10,70),
        'gender' => $faker->randomElement(["Male","Female"]),
        'weight' => $faker->randomFloat(2,4,100),
        'height' => $faker->numberBetween(30,240),
    ];
});
