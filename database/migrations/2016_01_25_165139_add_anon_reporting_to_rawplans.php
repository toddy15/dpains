<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnonReportingToRawplans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rawplans', function (Blueprint $table) {
            $table->boolean('anon_report')->after('shifts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rawplans', function (Blueprint $table) {
            $table->dropColumn('anon_report');
        });
    }
}
