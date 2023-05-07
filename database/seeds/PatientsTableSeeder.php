<?php
/*
 * File name: DoctorsTableSeeder.php
 * Last modified: 2021.03.01 at 21:22:30
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('patients')->delete();
        factory(Patient::class, 20)->create();
    }
}
