<?php
/*
 * File name: ClinicTest.php
 * Last modified: 2022.04.03 at 12:56:23
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace Models;

use App\Models\Clinic;
use Carbon\Carbon;
use Tests\TestCase;

class ClinicTest extends TestCase
{

    public function testGetAvailableAttribute()
    {
        $clinic = Clinic::find(17);
        $this->assertTrue($clinic->available);
        $this->assertTrue($clinic->accepted);
        $this->assertTrue($clinic->openingHours()->isOpenAt(new Carbon('2021-02-05 12:00:00')));
    }

    public function testOpeningHours()
    {
        $clinic = Clinic::find(17);
        $open = $clinic->openingHours()->isOpenAt(new Carbon('2021-02-05 12:00:00'));
        $this->assertTrue($open);
    }

    public function testWeekCalendar()
    {
        $clinic = Clinic::find(17);
        $dates = $clinic->weekCalendar(Carbon::now());
        $this->assertIsArray($dates);
    }

    public function testGetHasValidSubscriptionAttribute()
    {
        $clinic = Clinic::find(15);
        $this->assertTrue($clinic->has_valid_subscription);
    }
}
