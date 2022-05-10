<?php

use App\Models\Comment;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

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

test('a guest cannot create a comment', function () {
    $comment = Comment::factory()->raw();

    post(route('comments.store', $comment))->assertRedirect(route('login'));
});

test('a user can create a comment', function () {
    actingAs(User::factory()->create());

    $comment = Comment::factory()->raw();
    get(route('comments.index'))->assertDontSeeText($comment['comment']);

    get(route('comments.create'))->assertSeeText('Bemerkung:');

    post(route('comments.store', $comment))->assertRedirect(
        route('comments.index'),
    );

    get(route('comments.index'))->assertSeeText($comment['comment']);
});
