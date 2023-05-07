<?php
/*
 * File name: AwardFactory.php
 * Last modified: 2021.01.17 at 20:46:36
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */


use App\Models\Award;
use App\Models\Clinic;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Award::class, function (Faker $faker) {
    return [
        'title' => $faker->text(100),
        'description' => $faker->realText(),
        'clinic_id' => Clinic::all()->random()->id
    ];
});

$factory->state(Award::class, 'title_more_127_char', function (Faker $faker) {
    return [
        'title' => $faker->paragraph(20),
    ];
});

$factory->state(Award::class, 'not_exist_clinic_id', function (Faker $faker) {
    return [
        'clinic_id' => 500000, // not exist id
    ];
});
