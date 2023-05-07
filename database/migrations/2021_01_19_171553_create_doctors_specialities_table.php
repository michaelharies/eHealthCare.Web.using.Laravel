<?php
/*
 * File name: 2021_01_19_171553_create_doctor_specialities_table.php
 * Last modified: 2021.01.22 at 11:37:49
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsSpecialitiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('doctor_specialities');
        Schema::create('doctor_specialities', function (Blueprint $table) {
            $table->integer('doctor_id')->unsigned();
            $table->integer('speciality_id')->unsigned();
            $table->primary(['doctor_id', 'speciality_id']);
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('speciality_id')->references('id')->on('specialities')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('doctor_specialities');
    }
}
