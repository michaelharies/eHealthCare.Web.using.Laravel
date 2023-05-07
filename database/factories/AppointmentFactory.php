<?php
/*
 * File name: BookingFactory.php
 * Last modified: 2022.02.16 at 11:47:03
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */


use App\Models\Address;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Clinic;
use App\Models\Coupon;
use App\Models\Doctor;
use App\Models\DoctorPatients;
use App\Models\Patient;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Appointment::class, function (Faker $faker) {

    $userId = User::role('customer')->get()->random()->id;
    $address =  $faker->randomElement(Address::where('user_id','=',$userId)->get()->toArray());
    $appointmentStatus =  $faker->randomElement(AppointmentStatus::get()->toArray());
    $coupon =  $faker->randomElement(Coupon::get()->toArray());
    $appointmentAt = $faker->dateTimeBetween('-7 months','70 hours');
    $startAt = $faker->dateTimeBetween('75 hours','80 hours');
    $endsAt = $faker->dateTimeBetween('81 hours','85 hours');
    $patient = $faker->randomElement(Patient::where('user_id', '=', $userId)->get()->toArray());
    $clinic = $faker->randomElement(Clinic::where('accepted', '=', '1')->get()->toArray());
    $clinic_id = $clinic['id'];
    $doctor = $faker->randomElement(Doctor::where('clinic_id', '=', $clinic_id)->get()->toArray());
    if ($doctor==null){
        //$doctor = $faker->randomElement(Doctor::where('clinic_id', '=', $clinic_id)->get()->toArray());
        $doctor = $faker->randomElement(Doctor::all()->toArray());
    }

    return [
        'clinic' => $clinic,
        'doctor' => $doctor,
        'patient' => $patient,
        'quantity' => 1,
        'user_id' => $userId,
        'appointment_status_id' => $appointmentStatus['id'],
        'coupon' => $coupon,
        'address' => $address,
        'taxes' => Clinic::find($clinic['id'])->taxes,
        'appointment_at' => $appointmentAt,
        'start_at' => $appointmentStatus['order'] >= 40 ? $startAt : null,
        'ends_at' => $appointmentStatus['order'] >= 50 ? $endsAt : null,
        'hint' => $faker->sentence,
        'cancel' => $faker->boolean(5),
        'online' => $faker->boolean(),
    ];
});
