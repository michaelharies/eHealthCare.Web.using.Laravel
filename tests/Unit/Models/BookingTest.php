<?php
/*
 * File name: AppointmentTest.php
 * Last modified: 2021.01.29 at 23:27:25
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace Models;

use App\Models\Appointment;
use App\Models\Coupon;
use App\Models\Doctor;
use App\Models\Option;
use App\Models\Tax;
use Tests\TestCase;

class AppointmentTest extends TestCase
{


    public function testGetDurationInHours()
    {
        $appointment = new Appointment([
            'start_at' => '2021-09-17 11:00:00',
            'ends_at' => '2021-09-17 12:00:00'
        ]);
        $duration = $appointment->getDurationInHours();
        $this->assertEquals(1, $duration, 'Duration is 1 hour');

        $appointment = new Appointment([
            'start_at' => '2021-09-17 09:30:00',
            'ends_at' => '2021-09-17 21:00:00'
        ]);
        $duration = $appointment->getDurationInHours();
        $this->assertEquals(11.5, $duration, 'Duration is 11.5 hours');
    }

    public function testGetSubtotal()
    {
        $doctor = Doctor::all()->random();
        $options = Option::all();
        $appointment = new Appointment([
            'doctor' => $doctor,
            'options' => $options,
            'start_at' => '2021-09-17 11:00:00',
            'ends_at' => '2021-09-17 15:00:00'
        ]);
        $subtotal = ((5 * 4) + 159 + 199);
        $this->assertEquals($subtotal, $appointment->getSubtotal());
    }

    public function testGetTotal()
    {
        $doctor = Doctor::all()->random();
        $options = Option::all();
        $taxes = Tax::all();
        $coupon = Coupon::find(1);
        $appointment = new Appointment([
            'taxes' => $taxes,
            'coupon' => $coupon,
            'doctor' => $doctor,
            'options' => $options,
            'start_at' => '2021-01-26 17:42:00',
            'ends_at' => '2021-01-26 19:42:00'
        ]);
        $subtotal = ((5 * 2) + 159 + 199);
        dump($appointment->getTotal());
        $this->assertEquals($subtotal + 20 + ($subtotal * 0.1) + ($subtotal * 0.23) - 2, $appointment->getTotal());
    }
}
