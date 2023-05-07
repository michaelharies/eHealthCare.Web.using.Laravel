<?php
/*
 * File name: DoctorSpecialitiesTableSeeder.php
 * Last modified: 2021.03.02 at 14:35:42
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use App\Models\DoctorSpeciality;
use Illuminate\Database\Seeder;

class DoctorSpecialitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('doctor_specialities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        try {
            factory(DoctorSpeciality::class, 40)->create();
        } catch (Exception $e) {
        }
        try {
            factory(DoctorSpeciality::class, 40)->create();
        } catch (Exception $e) {
        }
        try {
            factory(DoctorSpeciality::class, 40)->create();
        } catch (Exception $e) {
        }


    }
}
