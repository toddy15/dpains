<?php

use Illuminate\Database\Seeder;

class StaffgroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staffgroups = [
            'CA',
            'CA-Vertreter',
            'LOA',
            'OA',
            'FA',
            'WB mit Nachtdienst',
            'WB',
            'Bundeswehr',
            'Hospitation',
            'Chirurg',
            'Dummy',
        ];
        $weight = 1;
        foreach ($staffgroups as $staffgroup) {
            DB::table('staffgroups')->insert([
                'staffgroup' => $staffgroup,
                'weight' => $weight,
            ]);
            $weight++;
        }
    }
}
