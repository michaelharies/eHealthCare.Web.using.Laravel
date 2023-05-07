<?php
/*
 * File name: Updatev110Seeder.php
 * Last modified: 2021.09.16 at 12:29:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

use Illuminate\Database\Seeder;

class Updatev110Seeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AppSettingsTableV110Seeder::class);

    }
}
