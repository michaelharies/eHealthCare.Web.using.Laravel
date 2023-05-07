<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateToV101 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('earnings')) {
            Schema::table('earnings', function (Blueprint $table) {
                $table->integer('doctor_id')->nullable()->unsigned()->after('clinic_id');
                $table->double('doctor_earning', 10, 2)->default(0)->after('clinic_earning');

                $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('set null')->onUpdate('set null');

            });
        }
        if (Schema::hasTable('doctors')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->double('commission', 5, 2)->default(0);


            });
        }
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
