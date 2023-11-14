<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Planparser;
use Tests\Seeders\EpisodesSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;

test('a guest cannot access the BD reports page', function () {
    get(route('reports.showbds'))
        ->assertRedirect(route('login'));
});

test('a user can access the BD reports page', function () {
    actingAs(User::factory()->create());

    seed(EpisodesSeeder::class);
    $people = file_get_contents('tests/datasets/2024-01-people.txt');
    $shifts = file_get_contents('tests/datasets/2024-01-shifts.txt');

    $p = new Planparser('2024-01', $people, $shifts);
    $p->storeShiftsForPeople();

    get(route('reports.showbds'))
        ->assertOk();
});
