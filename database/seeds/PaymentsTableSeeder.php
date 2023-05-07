<?php
/*
 * File name: PaymentsTableSeeder.php
 * Last modified: 2021.03.01 at 21:35:31
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class PaymentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        //DB::table('payments')->truncate();
        $appointments = Appointment::all();
        foreach ($appointments as $appointment) {
            DB::table('payments')->insert(array(
                'id' => $appointment->id,
                'amount' => $appointment->getTotal(),
                'description' => 'appointment ' . $appointment->id,
                'user_id' => $appointment->user_id,
                'payment_method_id' => 6,
                'payment_status_id' => in_array($appointment->appointment_status_id, [6,5,4]) ? 2 : 1,
                'updated_at' => $appointment->appointment_at,
                'created_at' => $appointment->appointment_at,
            ));
            $appointment->payment_id = $appointment->id;
            $appointment->save();
        }
    }
}
