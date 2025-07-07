<?php

declare(strict_types=1);

use App\Models\DueShift;
use App\Models\Staffgroup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

test('a guest cannot view the due shifts', function (): void {
    $this->get(route('due_shifts.index'))->assertRedirect(route('login'));
});

test('a user can view the due shifts', function (): void {
    actingAs(User::factory()->create());
    $this->get(route('due_shifts.index'))
        ->assertOk()
        ->assertViewIs('due_shifts.index')
        ->assertViewHas('due_shifts');
});

test('a user can create a due shift', function (): void {
    actingAs(User::factory()->create());

    $due_shift = DueShift::factory()->raw();
    $staffgroup = Staffgroup::find($due_shift['staffgroup_id']);

    $this->get(route('due_shifts.index'))
        ->assertDontSeeText($staffgroup['staffgroup'])
        ->assertDontSeeText($due_shift['nefs'])
        ->assertDontSeeText($due_shift['nefs']);

    post(route('due_shifts.store', $due_shift))->assertRedirect(
        route('due_shifts.index'),
    );

    $this->get(route('due_shifts.index'))
        ->assertSeeText($staffgroup['staffgroup'])
        ->assertSeeText($due_shift['nights'])
        ->assertSeeText($due_shift['nefs']);
});
