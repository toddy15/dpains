<?php

use App\Models\User;
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
        ->assertOk();
});
