<?php

use App\Models\Employee;
use App\Models\Episode;
use App\Models\User;
use App\Services\Helper;
use Illuminate\Http\Request;
use Tests\Seeders\EpisodesSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\seed;

it('redirects when the hash is not valid', function (): void {
    $this->get(route('anon.showYear', ['year' => 2023, 'hash' => 'invalid-hash']))
        ->assertRedirectToRoute('homepage');

    expect(session('warning'))->toBe('Dieser Zugriffcode ist nicht gültig.');
});

test('it returns a 404 when there is no planned month for the year', function (): void {
    $this->mock(Helper::class)
        ->shouldReceive('getPlannedMonthForAnonAccess')
        ->with(2023)
        ->andReturn(null);

    Employee::factory()->create(['hash' => 'valid-hash']);
    $this->get(route('anon.showYear', ['year' => 2023, 'hash' => 'valid-hash']))
        ->assertStatus(404);
});

test('it returns the expected view with the correct data', function (): void {
    // Set up a rawplan
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $people = file_get_contents('tests/datasets/2024-01_standard-people.txt');
    $shifts = file_get_contents('tests/datasets/2024-01_standard-shifts.txt');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => $people,
        'shifts' => $shifts,
        'anon_report' => true,
    ]);

    $employee = Employee::factory()->create(['hash' => 'valid-hash']);
    Episode::factory()->create([
        'employee_id' => $employee->id,
        'start_date' => '2024-07',
    ]);

    $helper = $this->mock(Helper::class);
    $helper->shouldReceive('getPlannedMonthForAnonAccess')
        ->with(2024)
        ->andReturn('2024-07');
    $helper->shouldReceive('getWorkedMonth')
        ->with(2024)
        ->andReturn('2024-05');
    $helper->shouldReceive('getPreviousYearUrl')
        ->with('anon/', 2024)
        ->andReturn('/anon/2023');
    $helper->shouldReceive('getNextYearUrl')
        ->with('anon/', 2024)
        ->andReturn('/anon/2025');
    $helper->shouldReceive('getTablesForYear')
        ->with(
            $this->mock(Request::class),
            2024,
            '2024-07',
            $employee->id,
        )
        ->andReturn([]);

    //    $response = get(route('anon.showYear', ['year' => 2024, 'hash' => 'valid-hash']));
    //
    //    $response->assertViewIs('anon.showYear')
    //        ->assertViewHas('hash', 'valid-hash')
    //        ->assertViewHas('year', 2023)
    //        ->assertViewHas('previous_year_url', '/anon/2022/valid-hash')
    //        ->assertViewHas('next_year_url', '/anon/2024/valid-hash')
    //        ->assertViewHas('readable_planned_month', 'July 2023')
    //        ->assertViewHas('readable_worked_month', 'May 2023');
})->todo();
