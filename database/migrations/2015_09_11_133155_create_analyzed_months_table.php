<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalyzedMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analyzed_months', function (Blueprint $table) {
            $table->increments('id');
            $table->string('month', 7);
            $table->integer('number');
            $table->integer('nights');
            $table->integer('nefs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('analyzed_months');
    }
}
