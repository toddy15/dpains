<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analyzed_months', function (Blueprint $table): void {
            $table->decimal('bds', 8, 1)->after('cons');
        });
    }
};
