<?php

declare(strict_types=1);

use App\Models\Episode;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('a user can create a new episode for a new employee', function () {
    actingAs(User::factory()->create());
    // This creates already an employee, and there are some
    // from the EpisodeSeeder, so the next employee has ID 30.
    // @todo: find a way to get the correct next employee ID automatically.
    $episode = Episode::factory()->make();
    $data = $episode->toArray();
    $data['employee_id'] = 0;
    $data['month'] = $episode->month;
    $data['year'] = $episode->year;

    post(route('episodes.store', $data))
        ->assertRedirect(route('employees.episodes.index', ['employee' => 30]));
});

test('a user can create a new episode for an existing employee', function () {
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
    get(route('employees.episodes.index', ['employee' => $episode_1->employee_id]))
        ->assertSeeText($episode_1->name)
        ->assertDontSeeText($episode_2->name);

    post(route('episodes.store', $data))
        ->assertRedirect(route('employees.episodes.index', ['employee' => $episode_1->employee_id]));

    // Now there should be two episodes with a name
    get(route('employees.episodes.index', ['employee' => $episode_1->employee_id]))
        ->assertSeeText($episode_1->name)
        ->assertSeeText($episode_2->name);
});
