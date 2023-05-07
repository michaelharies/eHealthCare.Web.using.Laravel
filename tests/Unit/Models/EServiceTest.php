<?php
/*
 * File name: DoctorTest.php
 * Last modified: 2021.02.05 at 13:07:43
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace Models;

use App\Models\Doctor;
use Tests\TestCase;

class DoctorTest extends TestCase
{

    public function testGetAvailableAttribute()
    {
        $doctor = Doctor::find(32);
        self::assertTrue($doctor->available);
    }
}
