<?php

use Illuminate\Database\Seeder;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find out the highest number currently in use
        $highest_person_number = DB::table('episodes')->max('employee_id');
        // Insert blank entries for all ids currently in use.
        $time = date('Y-m-d H:i:s');
        for ($id = 1; $id <= $highest_person_number; $id++) {
            DB::table('employees')->insert([
                'id' => $id,
                'email' => str_random(),
                'hash' => str_random(),
                'created_at' => $time,
                'updated_at' => $time
            ]);
        }
    }
}
