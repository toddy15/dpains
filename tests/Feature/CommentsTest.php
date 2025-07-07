<?php

use App\Models\Comment;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('a guest cannot view the comments', function (): void {
    $this->get(route('comments.index'))->assertRedirect(route('login'));
});

test('a user can view the comments', function (): void {
    actingAs(User::factory()->create());
    $this->get(route('comments.index'))
        ->assertOk()
        ->assertViewIs('comments.index')
        ->assertViewHas('comments');
});

test('a guest cannot create a comment', function (): void {
    $comment = Comment::factory()->raw();

    post(route('comments.store', $comment))->assertRedirect(route('login'));
});

test('a user can create a comment', function (): void {
    actingAs(User::factory()->create());

    $comment = Comment::factory()->raw();
    $this->get(route('comments.index'))->assertDontSeeText($comment['comment']);

    $this->get(route('comments.create'))->assertSeeText('Bemerkung:');

    post(route('comments.store', $comment))->assertRedirect(
        route('comments.index'),
    );

    $this->get(route('comments.index'))->assertSeeText($comment['comment']);
});

test('a guest cannot edit a comment', function (): void {
    $comment = Comment::factory()->create();
    $this->get(route('comments.edit', $comment->id))->assertRedirect(route('login'));
});

test('a user can edit a comment', function (): void {
    $comment = Comment::factory()->create();
    actingAs(User::factory()->create());
    $this->get(route('comments.edit', $comment->id))
        ->assertOk()
        ->assertViewIs('comments.edit')
        ->assertViewHas('comment')
        ->assertSee($comment->comment);
});

test('a guest cannot update a comment', function (): void {
    $comment = Comment::factory()->create();
    put(route('comments.update', $comment->id), [
        'comment' => 'This is a new comment.',
    ])->assertRedirect(route('login'));
});

test('a user can update a comment', function (): void {
    $comment = Comment::factory()->create();
    assertDatabaseMissing('comments', ['comment' => 'This is a new comment.']);

    actingAs(User::factory()->create());
    put(route('comments.update', $comment->id), [
        'comment' => 'This is a new comment.',
    ])->assertRedirect(route('comments.index'));

    assertDatabaseHas('comments', ['comment' => 'This is a new comment.']);
});
