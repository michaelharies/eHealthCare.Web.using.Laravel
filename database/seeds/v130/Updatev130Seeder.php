<?php

use Illuminate\Database\Seeder;

class Updatev130Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsTableV130Seeder::class);
    }
}
