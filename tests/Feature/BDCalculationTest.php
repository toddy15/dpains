<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Episode;
use App\Models\Staffgroup;

test('it calculates the maximal number of BDs for an amployee', function () {
    $vk_per_month = [1 => 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];
    _setup_tables($vk_per_month);
});

function _setup_tables(array $vk_per_month): void
{
    // We need a staffgroup, but it can be with random data.
    $staffgroup = Staffgroup::factory()->create();
    // We need an employee, but it can be with random data.
    $employee = Employee::factory()->create();

    // Generate episodes with variable VK data for BD calculations
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
