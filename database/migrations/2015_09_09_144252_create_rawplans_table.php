<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rawplans', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7)->unique();
            $table->text('people');
            $table->text('shifts');
            $table->boolean('anon_report');
            $table->timestamps();
        });
    }
};
