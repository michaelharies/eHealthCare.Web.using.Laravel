<?php
/*
 * File name: AppointmentsTableSeeder.php
 * Last modified: 2021.03.01 at 21:41:49
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('appointments')->delete();
        DB::table('appointments')->truncate();
        factory(Appointment::class, 20)->create();
//        try {
//            factory(Appointment::class, 20)->create();
//        }catch (Exception $e){}

    }
}
