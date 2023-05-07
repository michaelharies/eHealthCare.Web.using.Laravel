<?php
/*
 * File name: 2021_01_30_135356_create_earnings_table.php
 * Last modified: 2021.01.30 at 14:55:22
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarningsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('earnings');
        Schema::create('earnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('clinic_id')->nullable()->unsigned();
            $table->integer('doctor_id')->nullable()->unsigned();
            $table->integer('total_appointments')->unsigned()->default(0);
            $table->double('total_earning', 10, 2)->default(0);
            $table->double('admin_earning', 10, 2)->default(0);
            $table->double('clinic_earning', 10, 2)->default(0);
            $table->double('doctor_earning', 10, 2)->default(0);
            $table->double('taxes', 10, 2)->default(0);
            $table->timestamps();
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('set null')->onUpdate('set null');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('set null')->onUpdate('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('earnings');
    }
}
