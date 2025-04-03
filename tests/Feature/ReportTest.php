<?php

use App\Models\User;
use Tests\Seeders\EpisodesSeeder;
use Tests\Seeders\NormalPlanSeeder;

use function Pest\Laravel\actingAs;

test('guests cannot view the reports page', function () {
    $this->get('/report')
        ->assertRedirect(route('login'));
});

it('can view the reports page', function () {
    $this->seed(NormalPlanSeeder::class);
    actingAs(User::factory()->create());
    $this->get('/report/2023')
        ->assertOk()
        ->assertSee('Auswertung für 2023')
        ->assertDontSee('Seite nicht vorhanden');
});

it('does not change the report page during refactoring', function () {
    $this->seed(EpisodesSeeder::class);
    loadDataset('2024-01_standard');

    // Provide a constant username for snapshot testing
    actingAs(User::factory(['name' => 'Testuser'])->create());

    $response = $this->get('/report/2024');
    $response->assertOk()
        ->assertSee('Auswertung für 2024')
        ->assertDontSee('Seite nicht vorhanden');

    expect($response->content())->toMatchSnapshot();
});

it('calculates the number of nightshifts', function () {
    $this->seed(EpisodesSeeder::class);
    loadDataset('2025-01_nightshifts');

    // Provide a constant username for snapshot testing
    actingAs(User::factory(['name' => 'Testuser'])->create());

    $response = $this->get('/report/2025');
    $response->assertOk()
        ->assertSee('Auswertung für 2025')
        ->assertDontSee('Seite nicht vorhanden');

    expect($response->content())->toMatchSnapshot();
});

it('calculates the number of BDs', function () {
    $this->seed(EpisodesSeeder::class);
    loadDataset('2025-01_nightshifts');

    // Provide a constant username for snapshot testing
    actingAs(User::factory(['name' => 'Testuser'])->create());

    $response = $this->get('/report/bd/2025');
    $response->assertOk()
        ->assertSee('Bereitschaftsdienste 2025')
        ->assertDontSee('Seite nicht vorhanden');

    expect($response->content())->toMatchSnapshot();
});
