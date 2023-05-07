<?php
/*
 * File name: DoctorFactory.php
 * Last modified: 2021.11.15 at 12:38:59
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

/** @var Factory $factory */

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Doctor::class, function (Faker $faker) {
    $doctors = [
        'Dr. Darren Elder', 'Dr. Deborah Angel', 'Dr. John Gibbs', 'Dr. Katharine Sofia', 'Dr. Linda Tobin', 'Dr. Marvin Campbell', 'Dr. Olga Barlow', 'Dr. Paul Richard', 'Dr. Ruby Paul', 'Dr. John Perrin', 'Dr. Paul Brient', 'Dr. Lois Di Nominator', 'Dr. Karen Onnabit', 'Dr. Ray Sin', 'Dr. Darren Campbell', 'Dr. Cherry Blossom', 'Dr. Hank R. Cheef', 'Dr. Olive Yew', 'Dr. Toi Story','Dr. Rod Knee', 'Dr. Mary Krismass'

    ];
    $price = $faker->randomFloat(2, 10, 50);
    $discountPrice = $price - $faker->randomFloat(2, 1, 10);
    return [
        'name' => $faker->randomElement($doctors),
        'price' => $price,
        'discount_price' => $faker->randomElement([$discountPrice, 0]),
        'description' => $faker->text,
        'featured' => $faker->boolean,
        'enable_appointment' => $faker->boolean,
        'available' => $faker->boolean,
        'commission' => $faker->numberBetween(10,90),
        'clinic_id' => Clinic::all()->random()->id,
        'user_id' => $faker->randomElement(User::role('doctor')->get()),
    ];
});
