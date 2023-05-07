<?php
/*
 * File name: AwardsTableSeeder.php
 * Last modified: 2021.03.01 at 21:40:37
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('coupons')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        factory(Coupon::class, 5)->create();
    }
}
