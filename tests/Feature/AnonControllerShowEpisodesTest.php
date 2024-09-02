<?php

use App\Models\Employee;
use App\Models\Episode;
use Illuminate\Support\Carbon;

use function Pest\Laravel\get;

test('showEpisodes with valid hash returns correct view', function () {
    $employee = Employee::factory()->create([
        'hash' => 'valid_hash',
    ]);
    Episode::factory()->count(3)->create([
        'employee_id' => $employee->id,
        'name' => 'John Doe',
    ]);

    $response = get(route('anon.episodes', ['hash' => 'valid_hash']))
        ->assertOk()
        ->assertViewHas('hash', 'valid_hash')
        ->assertViewHas('episodes')
        ->assertViewHas('latest_name', 'John Doe');

    expect($response->viewData('episodes'))->toHaveCount(3);
});

test('showEpisodes with invalid hash redirects to homepage with warning', function () {
    $response = get(route('anon.episodes', ['hash' => 'invalid_hash']))
        ->assertRedirectToRoute('homepage');

    expect(session('warning'))->toBe('Dieser Zugriffcode ist nicht gültig.');
});

test('showEpisodes updates last access time for employee', function () {
    Carbon::setTestNow(Carbon::now());
    $employee = Employee::factory()->create(['hash' => 'valid_hash']);
    Episode::factory()->create(['employee_id' => $employee->id]);
    $originalUpdatedAt = $employee->updated_at;

    Carbon::sleep(1); // Ensure a time difference
    get(route('anon.episodes', ['hash' => 'valid_hash']))
        ->assertOK();

    $employee->refresh();
    expect($employee->updated_at)->toBeGreaterThan($originalUpdatedAt);
});

test('showEpisodes orders episodes by start_date in ascending order', function () {
    $employee = Employee::factory()->create(['hash' => 'valid_hash']);

    $episode1 = Episode::factory()->create([
        'employee_id' => $employee->id,
        'start_date' => '2024-04',
    ]);
    $episode2 = Episode::factory()->create([
        'employee_id' => $employee->id,
        'start_date' => '2023-04',
    ]);
    $episode3 = Episode::factory()->create([
        'employee_id' => $employee->id,
        'start_date' => '2022-04',
    ]);

    $response = get(route('anon.episodes', ['hash' => 'valid_hash']))
        ->assertOK();

    $episodes = $response->viewData('episodes');

    expect($episodes[0]->id)->toBe($episode3->id);
    expect($episodes[1]->id)->toBe($episode2->id);
    expect($episodes[2]->id)->toBe($episode1->id);
});

test('showEpisodes only returns episodes for the specified employee', function () {
    $employee1 = Employee::factory()->create(['hash' => 'hash1']);
    $employee2 = Employee::factory()->create(['hash' => 'hash2']);

    Episode::factory()->count(2)->create(['employee_id' => $employee1->id]);
    Episode::factory()->count(3)->create(['employee_id' => $employee2->id]);

    $response = get(route('anon.episodes', ['hash' => 'hash1']))
        ->assertOk();

    expect($response->viewData('episodes'))->toHaveCount(2);
});
