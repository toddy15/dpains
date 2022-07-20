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

    get(
        action(
            [EmployeeController::class, 'showMonth'],
            ['year' => 2022, 'month' => 5],
        ),
    )->assertOk();

    get(
        action(
            [EmployeeController::class, 'showMonth'],
            ['year' => 2022, 'month' => 5],
        ),
    )->assertOk();

    get(route('employees.episodes.index', $employee))->assertOk();
});

test('a user can view the VK for a year', function () {
    actingAs(User::factory()->create());

    get(
        action(
            [EmployeeController::class, 'showVKForYear'],
            ['which_vk' => 'all', 'year' => 2021],
        ),
    )
        ->assertSeeText('Übersicht der VK für 2021')
        ->assertDontSeeText('(Nächte)')
        ->assertDontSeeText('(NEF)')
        ->assertOk();

    get(
        action(
            [EmployeeController::class, 'showVKForYear'],
            ['which_vk' => 'night', 'year' => 2018],
        ),
    )
        ->assertSeeText('Übersicht der VK für 2018')
        ->assertSeeText('(Nächte)')
        ->assertDontSeeText('(NEF)')
        ->assertOk();

    get(
        action(
            [EmployeeController::class, 'showVKForYear'],
            ['which_vk' => 'nef', 'year' => 2016],
        ),
    )
        ->assertSeeText('Übersicht der VK für 2016')
        ->assertDontSeeText('(Nächte)')
        ->assertSeeText('(NEF)')
        ->assertOk();

    get(
        action(
            [EmployeeController::class, 'showVKForYear'],
            ['which_vk' => 'non-existing-code', 'year' => 2023],
        ),
    )
        ->assertSeeText('Übersicht der VK für 2023')
        ->assertDontSeeText('(Nächte)')
        ->assertDontSeeText('(NEF)')
        ->assertOk();
});
