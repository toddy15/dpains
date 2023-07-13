<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Tests\Seeders\NormalPlanSeeder;

test('guests cannot view the reports page', function () {
    get('/report')
        ->assertRedirect(route('login'));
});

it('can view the reports page', function () {
    $this->seed(NormalPlanSeeder::class);
    actingAs(User::factory()->create());
    get('/report/2023')
        ->assertOk();
});
