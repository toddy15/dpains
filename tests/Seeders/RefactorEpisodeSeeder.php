<?php

declare(strict_types=1);

namespace Tests\Seeders;

use App\Models\Comment;
use App\Models\Employee;
use App\Models\Episode;
use App\Models\Staffgroup;
use Illuminate\Database\Seeder;

class RefactorEpisodeSeeder extends Seeder
{
    /**
     * Create a defined set of employees and staffgroups.
     *
     * This is needed for a refactoring of the employee SQL queries.
     * Include some past, present, and future employees.
     *
     * The testing date must be set to 2025-09.
     */
    public function run(): void
    {
        // We need a staffgroup, but it can be with random data.
        $staffgroup = Staffgroup::factory()->create();
        // Create a specific comment for terminations.
        $end = Comment::factory()->create(['comment' => 'Vertragsende']);

        $data = [
            'Person, Single episode' => ['2016-01'],
            'Person, Multiple episodes' => ['2018-05', '2019-02', '2021-12', '2024-08'],
            'Person, Terminated' => ['2018-02', '2019-08', '2025-04 ENDE'],
            'Person, Terminated and started again' => ['2020-01', '2021-01 ENDE', '2022-01'],
            'Person, Multiple terminations' => ['2018-03', '2019-02 ENDE', '2020-01', '2021-02 ENDE', '2022-01', '2023-07 ENDE'],
            'Person, Future' => ['2025-10'],
            'Person, Future with termination' => ['2026-01', '2026-07 ENDE'],
        ];

        foreach ($data as $name => $startdates) {
            $employee = Employee::factory()->create();
            foreach ($startdates as $startdate) {
                $episode = [
                    'employee_id' => $employee->id,
                    'name' => $name,
                    'start_date' => $startdate,
                    'staffgroup_id' => $staffgroup->id,
                    'vk' => 1.0,
                    'factor_night' => 1.0,
                    'factor_nef' => 1.0,
                ];
                if (str_ends_with($startdate, ' ENDE')) {
                    $episode['comment_id'] = $end->id;
                    $episode['start_date'] = substr($startdate, 0, -5);
                }
                Episode::create($episode);
            }
        }
    }
}
