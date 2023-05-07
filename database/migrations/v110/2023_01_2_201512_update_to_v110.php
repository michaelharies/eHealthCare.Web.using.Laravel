<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateToV110 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (Schema::hasTable('doctors')) {
                Schema::table('doctors', function (Blueprint $table) {
                    $table->boolean('enable_online_consultation')->nullable()->default(0)->after('enable_at_clinic');
                });
            }

            if (Schema::hasTable('appointments')) {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->boolean('online')->nullable()->default(0)->after('cancel');
                });
            }
        }catch (Exception $exception){}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
