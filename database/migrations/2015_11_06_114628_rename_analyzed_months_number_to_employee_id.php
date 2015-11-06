<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAnalyzedMonthsNumberToEmployeeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyzed_months', function (Blueprint $table) {
            $table->renameColumn('number', 'employee_id');
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
            $table->renameColumn('employee_id', 'number');
        });
    }
}
