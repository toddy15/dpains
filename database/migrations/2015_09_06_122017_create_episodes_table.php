<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Employee;
use App\Models\Staffgroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained();
            $table->string('name');
            $table->string('start_date');
            $table->foreignIdFor(Staffgroup::class)->constrained();
            $table->decimal('vk', 4, 3);
            $table->decimal('factor_night', 4, 3);
            $table->decimal('factor_nef', 4, 3);
            $table
                ->foreignIdFor(Comment::class)
                ->nullable()
                ->constrained();
            $table->timestamps();
        });
    }
};
