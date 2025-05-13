<?php

namespace Tests\Seeders;

use App\Models\Episode;
use App\Models\Rawplan;
use App\Services\Helper;
use App\Services\Planparser;
use Illuminate\Database\Seeder;

class NormalPlanSeeder extends Seeder
{
    public function run(): void
    {
        $episode = Episode::factory()->create();
        $names = $episode->name."\n";

        $shifts = "1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\t1\n";
        $p = new Planparser('2023-01', $names, $shifts);

        $p->storeShiftsForPeople(new Helper);

        Rawplan::create([
            'month' => '2023-01',
            'people' => $names,
            'shifts' => $shifts,
            'anon_report' => false,
        ]);
    }
}
