<?php

use App\Models\User;
use Tests\Seeders\EpisodesSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\seed;

test('a guest cannot view the rawplans', function () {
    get(route('rawplans.index'))->assertRedirect(route('login'));
});

test('a user can view the rawplans', function () {
    actingAs(User::factory()->create());
    get(route('rawplans.index'))
        ->assertOk()
        ->assertViewIs('rawplans.index')
        ->assertViewHas('rawplans_planned');
});

test('a user can view the form for a rawplan upload', function () {
    actingAs(User::factory()->create());
    get(route('rawplans.create'))
        ->assertOk()
        ->assertViewIs('rawplans.create');
});

test('a user can create a rawplan', function () {
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $people = file_get_contents('tests/datasets/2024-01_standard-people.txt');
    $shifts = file_get_contents('tests/datasets/2024-01_standard-shifts.txt');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => $people,
        'shifts' => $shifts,
    ])
        ->assertRedirect(route('rawplans.index'));
});
