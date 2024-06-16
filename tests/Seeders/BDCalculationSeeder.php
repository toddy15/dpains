<?php

declare(strict_types=1);

namespace Tests\Seeders;

use App\Models\Employee;
use App\Models\Episode;
use App\Models\Staffgroup;
use Illuminate\Database\Seeder;

class BDCalculationSeeder extends Seeder
{
    /**
     * Create some employees for BD calculations.
     */
    public function run(): void
    {
        // We need a staffgroup, but it can be with random data.
        $staffgroup = Staffgroup::factory()->create();
        // We need an employee, but it can be with random data.
        $employee = Employee::factory()->create();

        // Generate episodes with variable VK data for BD calculations
        $vk_per_month = [1 => 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];
        foreach ($vk_per_month as $month => $vk) {
            Episode::factory()->create([
                'employee_id' => $employee->id,
                'start_date' => sprintf('2024-%02d', $month),
                'staffgroup_id' => $staffgroup->id,
                'vk' => $vk,
                'factor_night' => 1,
                'factor_nef' => 1,
            ]);
        }
    }
}
