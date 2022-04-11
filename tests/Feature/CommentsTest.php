<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('a guest cannot view the comments', function () {
    get(route('comments.index'))->assertRedirect(route('login'));
});

test('a user can view the comments', function () {
    actingAs(User::factory()->create());
    get(route('comments.index'))
        ->assertOk()
        ->assertViewIs('comments.index')
        ->assertViewHas('comments');
});
