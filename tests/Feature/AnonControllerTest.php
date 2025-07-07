<?php

use App\Models\Employee;
use Carbon\Carbon;

test('homepage returns correct view without hash', function (): void {
    $this->get(route('homepage'))
        ->assertOk()
        ->assertViewIs('homepage')
        ->assertViewHas('hash', '');
});

test('homepage returns correct view with valid hash', function (): void {
    // Ensure enough time has passed
    Carbon::setTestNow(Carbon::now());
    $employee = Employee::factory()->create(['hash' => 'valid_hash']);
    Carbon::sleep(1);

    $this->get(route('homepage', ['hash' => 'valid_hash']))
        ->assertOk()
        ->assertViewIs('homepage')
        ->assertViewHas('hash', 'valid_hash');

    $employee->refresh();
    expect($employee->updated_at)->toBeGreaterThan($employee->created_at);
});

test('homepage flashes warning for invalid hash', function (): void {
    $this->get(route('homepage', ['hash' => 'invalid_hash']))
        ->assertOk()
        ->assertViewIs('homepage')
        ->assertViewHas('hash', '');

    expect(session('warning'))->toBe('Dieser Zugriffcode ist nicht gültig.');
});

test('homepage updates last access time for valid hash', function (): void {
    // Ensure enough time has passed
    Carbon::setTestNow(Carbon::now());
    $employee = Employee::factory()->create(['hash' => 'valid_hash']);
    $originalUpdatedAt = $employee->updated_at;
    Carbon::sleep(1);

    $this->get(route('homepage', ['hash' => 'valid_hash']))
        ->assertOk();

    $employee->refresh();
    expect($employee->updated_at)->toBeGreaterThan($originalUpdatedAt);
});

test('homepage does not update last access time for invalid hash', function (): void {
    // Ensure enough time has passed
    Carbon::setTestNow(Carbon::now());
    $employee = Employee::factory()->create(['hash' => 'valid_hash']);
    $originalUpdatedAt = $employee->updated_at;
    Carbon::sleep(1);

    $this->get(route('homepage', ['hash' => 'invalid_hash']))
        ->assertOk();

    $employee->refresh();
    expect($employee->updated_at)->toEqual($originalUpdatedAt);
});
