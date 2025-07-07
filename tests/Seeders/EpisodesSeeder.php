<?php

namespace Tests\Seeders;

use App\Models\Employee;
use App\Models\Episode;
use App\Models\Staffgroup;
use Illuminate\Database\Seeder;

class EpisodesSeeder extends Seeder
{
    /**
     * Create a defined set of employees and staffgroups.
     *
     * This is needed so that later tests which depend on
     * episodes in the DB can assert correct values.
     *
     * We assume a small department:
     * - everybody starts 2016-01
     * - everybody has VK 1.0
     * - everybody has factors for nights and NEFs set to 1.0
     */
    public function run(): void
    {
        $people = [
            'Chefin, A' => 'CA',
            'Vertretung der Chefin, A' => 'CA-Vertreter',
            'Leitende Oberärztin, A' => 'LOA',
            'Oberärztin, A' => 'OA',
            'Oberärztin, B' => 'OA',
            'Fachärztin, A' => 'FA',
            'Fachärztin, B' => 'FA',
            'Fachärztin, C' => 'FA',
            'Ärztin, A' => 'WB mit Nachtdienst',
            'Ärztin, B' => 'WB',
            'Ärztin, C' => 'WB',
        ];

        // Keep track if the staffgroup needs to be created.
        $last_staffgroup = '';
        $staffgroup_weight = 1;
        foreach ($people as $person => $staffgroup_name) {
            if ($last_staffgroup !== $staffgroup_name) {
                $current_staffgroup = Staffgroup::factory()->create([
                    'staffgroup' => $staffgroup_name,
                    'weight' => $staffgroup_weight,
                ]);
                $staffgroup_weight += 1;
                $last_staffgroup = $staffgroup_name;
            }

            $employee = Employee::factory()->create();
            Episode::create([
                'employee_id' => $employee->id,
                'name' => $person,
                'start_date' => '2016-01',
                'staffgroup_id' => $current_staffgroup->id,
                'vk' => 1,
                'factor_night' => 1,
                'factor_nef' => 1,
            ]);
        }
    }
}
