<?php

use App\Models\Employee;
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
            $table->foreignIdFor(Employee::class)->constrained();
            $table->integer('nights');
            $table->integer('nefs');
            $table->integer('bus');
            $table->integer('cons');
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
        Schema::dropIfExists('analyzed_months');
    }
};
