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
};
