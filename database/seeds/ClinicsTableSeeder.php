<?php
/*
 * File name: ClinicsTableSeeder.php
 * Last modified: 2021.03.02 at 11:28:53
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use App\Models\Clinic;
use App\Models\ClinicTax;
use App\Models\ClinicUser;
use Illuminate\Database\Seeder;

class ClinicsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('clinics')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(Clinic::class, 18)->create();

        try {
            factory(ClinicUser::class, 10)->create();
        } catch (Exception $e) {
        }
        try {
            factory(ClinicUser::class, 10)->create();
        } catch (Exception $e) {
        }
        try {
            factory(ClinicUser::class, 10)->create();
        } catch (Exception $e) {
        }
        try {
            factory(ClinicTax::class, 10)->create();
        } catch (Exception $e) {
        }
        try {
            factory(ClinicTax::class, 10)->create();
        } catch (Exception $e) {
        }
        try {
            factory(ClinicTax::class, 10)->create();
        } catch (Exception $e) {
        }

    }
}
