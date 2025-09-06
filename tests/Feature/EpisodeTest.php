<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Employee;
use App\Models\Episode;
use App\Models\Staffgroup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

// Seed necessary data for tests
beforeEach(function (): void {
    Staffgroup::factory()->create(['staffgroup' => 'WB']);
    Comment::factory()->count(3)->create();
    Staffgroup::factory()->count(3)->create();
});

test('a user can create a new episode for a new employee', function (): void {
    actingAs(User::factory()->create());
    // This creates already an employee, and there are some
    // from the EpisodeSeeder, so the next employee has ID 52.
    // @todo: find a way to get the correct next employee ID automatically.
    $episode = Episode::factory()->make();
    $data = $episode->toArray();
    $data['employee_id'] = 0;
    $data['month'] = $episode->month;
    $data['year'] = $episode->year;

    post(route('episodes.store', $data))
        ->assertRedirect(route('employees.episodes.index', ['employee' => 41]));
});

test('a user can create a new episode for an existing employee', function (): void {
    actingAs(User::factory()->create());
    $episode_1 = Episode::factory()->create();
    $episode_2 = Episode::factory()->make();
    $data = $episode_2->toArray();
    $data['employee_id'] = $episode_1->employee_id;
    $data['month'] = $episode_2->month;
    $data['year'] = $episode_2->year;

    // Ensure that both names are different
    expect($episode_1->name)->not()->toBe($episode_2->name);

    // There should be only one episode with a name
    $this->get(route('employees.episodes.index', ['employee' => $episode_1->employee_id]))
        ->assertSeeText($episode_1->name)
        ->assertDontSeeText($episode_2->name);

    post(route('episodes.store', $data))
        ->assertRedirect(route('employees.episodes.index', ['employee' => $episode_1->employee_id]));

    // Now there should be two episodes with a name
    $this->get(route('employees.episodes.index', ['employee' => $episode_1->employee_id]))
        ->assertSeeText($episode_1->name)
        ->assertSeeText($episode_2->name);
});

it('returns the create view with default values', function (): void {
    actingAs(User::factory()->create());

    $employee = Employee::factory()->create();

    $response = get(route('episodes.create', ['employee_id' => $employee->id]));

    $response->assertStatus(200);
    $response->assertViewIs('episodes.create');
    $response->assertViewHas('episode');
    $response->assertViewHas('comments');
    $response->assertViewHas('staffgroups');
    $response->assertViewHas('start_year');
    $response->assertViewHas('end_year');
    $response->assertViewHas('month_names');
});

it('returns the edit view with episode data', function (): void {
    actingAs(User::factory()->create());
    $episode = Episode::factory()->create();

    $response = get(route('episodes.edit', ['episode' => $episode->id]));

    $response->assertStatus(200);
    $response->assertViewIs('episodes.edit');
    $response->assertViewHas('episode', $episode);
    $response->assertViewHas('comments');
    $response->assertViewHas('staffgroups');
    $response->assertViewHas('start_year');
    $response->assertViewHas('end_year');
    $response->assertViewHas('month_names');
});

it('updates an existing episode', function (): void {
    actingAs(User::factory()->create());
    $episode = Episode::factory()->create();
    $data = [
        'name' => 'Testname',
        'year' => 2023,
        'month' => 2,
        'staffgroup_id' => $episode->staffgroup_id,
        'vk' => 1.0,
        'factor_night' => 0.0,
        'factor_nef' => 0.0,
    ];

    $response = put(route('episodes.update', ['episode' => $episode->id]), $data);

    $response->assertRedirect(route('employees.episodes.index', ['employee' => $episode->employee_id]));
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'start_date' => '2023-02',
    ]);
});

it('deletes an episode', function (): void {
    actingAs(User::factory()->create());
    $episode = Episode::factory()->create();

    $response = delete(route('episodes.destroy', ['episode' => $episode->id]));

    $response->assertRedirect(route('employees.index'));
    $this->assertDatabaseMissing('episodes', ['id' => $episode->id]);
});
