<?php
/*
 * File name: ClinicAddressFactory.php
 * Last modified: 2021.04.20 at 11:19:32
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */


use App\Models\Address;
use App\Models\Clinic;
use App\Models\ClinicAddress;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(ClinicAddress::class, function (Faker $faker) {
    return [
        'address_id' => Address::all()->random()->id,
        'clinic_id' => Clinic::all()->random()->id
    ];
});
