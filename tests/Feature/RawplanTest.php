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

test('people are required for a rawplan', function () {
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $shifts = file_get_contents('tests/datasets/2024-01_standard-shifts.txt');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'shifts' => $shifts,
    ])
        ->assertRedirect(route('rawplans.create'))
        ->assertSessionHasErrors('people');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => '',
        'shifts' => $shifts,
    ])
        ->assertRedirect(route('rawplans.create'))
        ->assertSessionHasErrors('people');
});

test('shifts are required for a rawplan', function () {
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $people = file_get_contents('tests/datasets/2024-01_standard-people.txt');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => $people,
    ])
        ->assertRedirect(route('rawplans.create'))
        ->assertSessionHasErrors('shifts');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => $people,
        'shifts' => '',
    ])
        ->assertRedirect(route('rawplans.create'))
        ->assertSessionHasErrors('shifts');
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

it('checks that all expected people are there', function () {
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $people = file_get_contents('tests/datasets/2024-01_missing-people.txt');
    $shifts = file_get_contents('tests/datasets/2024-01_missing-shifts.txt');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => $people,
        'shifts' => $shifts,
    ])
        ->assertRedirect(route('rawplans.create'))
        ->assertSessionHasErrors(['people' => 'Die folgenden Mitarbeiter werden im Monat 2024-01 erwartet, aber nicht gefunden: Fachärztin, C']);
});

it('checks that not more people than expected are there', function () {
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $people = file_get_contents('tests/datasets/2024-01_added-people.txt');
    $shifts = file_get_contents('tests/datasets/2024-01_added-shifts.txt');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => $people,
        'shifts' => $shifts,
    ])
        ->assertRedirect(route('rawplans.create'))
        ->assertSessionHasErrors(['people' => 'Die folgenden Mitarbeiter werden im Monat 2024-01 nicht erwartet, aber gefunden: Fachärztin, D']);
});

it('detects missing days', function () {
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $people = file_get_contents('tests/datasets/2024-01_missing-days-people.txt');
    $shifts = file_get_contents('tests/datasets/2024-01_missing-days-shifts.txt');

    post(route('rawplans.store'), [
        'month' => '1',
        'year' => '2024',
        'people' => $people,
        'shifts' => $shifts,
    ])
        ->assertRedirect(route('rawplans.create'))
        ->assertSessionHasErrors(['shifts' => '2024-01: Die Anzahl der Tage in den Schichten stimmt nicht mit der Anzahl der Tage des Monats überein.']);
});
