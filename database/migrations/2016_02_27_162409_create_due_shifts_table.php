<?php

declare(strict_types=1);

use App\Models\Staffgroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
};
