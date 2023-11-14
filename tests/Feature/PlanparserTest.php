<?php

declare(strict_types=1);

use App\Services\Planparser;
use Tests\Seeders\EpisodesSeeder;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\seed;

it('parses a raw plan in planned state', function () {
    seed(EpisodesSeeder::class);

    $people = file_get_contents('tests/datasets/2024-01-people.txt');
    $shifts = file_get_contents('tests/datasets/2024-01-shifts.txt');

    assertDatabaseMissing('analyzed_months', ['month' => '2024-01']);

    $p = new Planparser('2024-01', $people, $shifts);
    $p->storeShiftsForPeople();

    assertDatabaseCount('analyzed_months', 11);
    assertDatabaseHas('analyzed_months', ['month' => '2024-01']);
});
