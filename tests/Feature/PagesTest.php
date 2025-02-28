<?php

declare(strict_types=1);

use App\Models\Staffgroup;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('a guest cannot access protected pages', function () {
    $this->get(route('employees.index'))->assertRedirect(route('login'));
    $this->get(route('rawplans.index'))->assertRedirect(route('login'));
    $this->get(route('rawplans.create'))->assertRedirect(route('login'));
    $this->get(route('episodes.create'))->assertRedirect(route('login'));
});

test('a user can access protected pages', function () {
    actingAs(User::factory()->create());

    $this->get(route('employees.index'))->assertOk();
    $this->get(route('rawplans.index'))->assertOk();

    // @TODO: Could this be rewritten to not require a 'WB' group?
    Staffgroup::factory()
        ->set('staffgroup', 'WB')
        ->create();
    $this->get(route('episodes.create'))->assertOk();
});
