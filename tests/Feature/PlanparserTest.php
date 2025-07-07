<?php

declare(strict_types=1);

use Tests\Seeders\EpisodesSeeder;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\seed;

it('parses a raw plan in planned state', function (): void {
    assertDatabaseMissing('analyzed_months', ['month' => '2024-01']);

    seed(EpisodesSeeder::class);
    loadDataset('2024-01_standard');

    assertDatabaseCount('analyzed_months', 11);
    assertDatabaseHas('analyzed_months', ['month' => '2024-01']);
});
