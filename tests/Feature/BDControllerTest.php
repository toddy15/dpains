<?php

declare(strict_types=1);

use App\Models\User;
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
    loadDataset('2024-01_standard');

    get(route('reports.showbds'))
        ->assertOk();
});
