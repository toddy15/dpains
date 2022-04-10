<?php

use App\Models\Staffgroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('due_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->foreignIdFor(Staffgroup::class)->constrained();
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
    public function down(): void
    {
        Schema::dropIfExists('due_shifts');
    }
};
