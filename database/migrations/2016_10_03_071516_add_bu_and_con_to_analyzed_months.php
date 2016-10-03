<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuAndConToAnalyzedMonths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyzed_months', function (Blueprint $table) {
            $table->integer('bus');
            $table->integer('cons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analyzed_months', function (Blueprint $table) {
            $table->dropColumn('bus');
            $table->dropColumn('cons');
        });
    }
}
