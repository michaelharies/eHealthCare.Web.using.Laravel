<?php
/*
 * File name: PaymentsTableSeeder.php
 * Last modified: 2021.03.01 at 21:35:31
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class DoctorPatientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::table('doctor_patients')->truncate();
            $appointments = Appointment::all();
            foreach ($appointments as $appointment) {
                DB::table('doctor_patients')->insert(array(
                    'patient_id' => $appointment->patient->id,
                    'doctor_id' => $appointment->doctor->id,
                ));
            }
        }catch (Exception $e){}

    }
}
