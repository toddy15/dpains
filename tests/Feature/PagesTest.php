<?php

declare(strict_types=1);

use App\Models\Staffgroup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('a guest cannot access protected pages', function () {
    get(route('employees.index'))->assertRedirect(route('login'));
    get(route('rawplans.index'))->assertRedirect(route('login'));
    get(route('rawplans.create'))->assertRedirect(route('login'));
    get(route('episodes.create'))->assertRedirect(route('login'));
});

test('a user can access protected pages', function () {
    actingAs(User::factory()->create());

    get(route('employees.index'))->assertOk();
    get(route('rawplans.index'))->assertOk();

    // @TODO: Could this be rewritten to not require a 'WB' group?
    Staffgroup::factory()
        ->set('staffgroup', 'WB')
        ->create();
    get(route('episodes.create'))->assertOk();
});
