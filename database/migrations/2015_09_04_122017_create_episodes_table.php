<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number');
            $table->string('name');
            $table->string('start_date');
            $table->integer('staffgroup_id')->references('id')->on('staffgroups');;
            $table->decimal('vk', 4, 3);
            $table->decimal('factor_night', 4, 3);
            $table->decimal('factor_nef', 4, 3);
            $table->integer('comment_id')->references('id')->on('comments');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('episodes');
    }
}
