<?php
/*
 * File name: 2021_01_19_135951_create_doctors_table.php
 * Last modified: 2021.11.15 at 12:44:22
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('doctors');
        Schema::create('doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('name')->nullable();
            $table->double('price', 10, 2)->default(0);
            $table->double('discount_price', 10, 2)->nullable()->default(0);
            $table->longText('description')->nullable();
            $table->boolean('featured')->nullable()->default(0);
            $table->boolean('enable_appointment')->nullable()->default(1);
            $table->boolean('enable_at_customer_address')->nullable()->default(1);
            $table->boolean('enable_at_clinic')->nullable()->default(1);
            $table->boolean('enable_online_consultation')->nullable()->default(0);
            $table->boolean('available')->nullable()->default(1);
            $table->double('commission', 5, 2)->default(0);
            $table->bigInteger('user_id')->unsigned();
            $table->integer('clinic_id')->unsigned();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('doctors');
    }
}
