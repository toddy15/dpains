<?php

use App\Http\Controllers\EmployeeController;
use App\Models\Employee;
use App\Models\Episode;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

test('a user can access pages', function () {
    actingAs(User::factory()->create());
    $episode = Episode::factory()->create();
    $employee = Employee::find($episode->employee_id);

    get(route('employees.index'))->assertOk();
    get(route('past.index'))->assertOk();
    get(route('employees.edit', $employee))->assertOk();
    put(
        route('employees.update', [
            'employee' => $employee->id,
            'email' => $employee->email,
        ]),
    )->assertRedirect(route('employees.index'));
});

/*
  GET|HEAD        employees/month/{year}/{month} .................................................... EmployeeController@showMonth
  GET|HEAD        employees/vk/{which_vk}/{year} ................................................ EmployeeController@showVKForYear
  GET|HEAD        employees/{id}/episodes ........................................................ EmployeeController@showEpisodes
 */
