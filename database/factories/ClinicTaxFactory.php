<?php
/*
 * File name: ClinicTaxFactory.php
 * Last modified: 2022.02.15 at 14:42:15
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */


use App\Models\Clinic;
use App\Models\ClinicTax;
use App\Models\Tax;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(ClinicTax::class, function (Faker $faker) {
    return [
        'tax_id' => Tax::all()->random()->id,
        'clinic_id' => Clinic::all()->random()->id
    ];
});
