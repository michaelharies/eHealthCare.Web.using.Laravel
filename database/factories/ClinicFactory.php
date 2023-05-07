<?php
/*
 * File name: ClinicFactory.php
 * Last modified: 2021.08.04 at 18:10:26
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */


use App\Models\Address;
use App\Models\Clinic;
use App\Models\ClinicLevel;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Clinic::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Public Health Clinic', 'Flu Shot Clinic', 'The Pain Medic', 'Universal Body Clinic', 'Animal Health Centre', 'Teen Clinic', 'Dentists', 'Mission Hospital Inc', 'High Point Medical Center', 'Annie Penn Memorial Clinic', 'Wayne Memorial', 'Medwest Harris', 'Blue Ridge Healthcare Clinics']) . " " . $faker->company,
        'description' => $faker->text,
        'address_id' => Address::all()->random()->id,
        'clinic_level_id' => ClinicLevel::all()->random()->id,
        'phone_number' => $faker->phoneNumber,
        'mobile_number' => $faker->phoneNumber,
        'availability_range' => $faker->randomFloat(2, 6000, 15000),
        'available' => $faker->boolean(95),
        'featured' => $faker->boolean(40),
        'accepted' => $faker->boolean(95),
    ];
});
