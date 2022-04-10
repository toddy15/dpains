<?php

use App\Models\Comment;
use App\Models\Employee;
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
    public function up()
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
            $table->foreignIdFor(Comment::class)->constrained();
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
        Schema::dropIfExists('episodes');
    }
};
