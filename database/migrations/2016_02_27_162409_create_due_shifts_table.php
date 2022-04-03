<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('due_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('year');
            $table->integer('staffgroup_id')->references('id')->on('staffgroups');
            $table->integer('nights');
            $table->integer('nefs');
            // Ensure that the year/staffgroup combination is unique.
            $table->unique(['year', 'staffgroup_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('due_shifts');
    }
};
