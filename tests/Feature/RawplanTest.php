<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

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

test('a user can create a rawplan');
