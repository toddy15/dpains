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
        Schema::create('analyzed_months', function (Blueprint $table) {
            $table->string('month', 7);
            $table->integer('employee_id')->references('id')->on('employees');
            $table->integer('nights');
            $table->integer('nefs');
            $table->primary(['month', 'employee_id']);
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
};
