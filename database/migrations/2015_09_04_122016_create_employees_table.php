<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->string('email', 64)->unique();
            $table->string('hash', 64);
            $table->string('bu_start')->nullable();
            $table->timestamps();
        });
    }
};
