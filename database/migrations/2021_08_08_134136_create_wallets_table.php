<?php
/*
 * File name: 2021_08_08_134136_create_wallets_table.php
 * Last modified: 2021.09.15 at 13:28:01
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('wallets');
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name', 191);
            $table->double('balance', 16, 2)->default(0);
            $table->longText('currency')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->boolean('enabled')->default(1)->nullable();
            $table->timestamps();
            $table->primary(['id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('wallets');
    }
}
