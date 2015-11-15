<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UserTableSeeder::class);
        $this->call(StaffgroupTableSeeder::class);
        $this->call(CommentTableSeeder::class);
        $this->call(EmployeeTableSeeder::class);

        Model::reguard();
    }
}
