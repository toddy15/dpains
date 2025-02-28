<?php

use App\Models\Employee;
use Illuminate\Support\Str;

test('logout with valid hash disables the hash and redirects to homepage', function () {
    $employee = Employee::factory()->create(['hash' => 'valid_hash']);

    $this->get(route('anon.logout', ['hash' => 'valid_hash']))
        ->assertRedirect(route('homepage'));

    $employee->refresh();
    expect($employee->hash)->not->toBe('valid_hash');
    expect(Str::length($employee->hash))->toBe(16); // Default length of Str::random()

    expect(session('info'))->toBe('Du wurdest abgemeldet.');
});

test('logout with invalid hash redirects to homepage with warning', function () {
    $this->get(route('anon.logout', ['hash' => 'invalid_hash']))
        ->assertRedirect(route('homepage'));

    expect(session('warning'))->toBe('Dieser Zugriffcode ist nicht gültig.');
});

test('logout does not affect other employees', function () {
    $employee1 = Employee::factory()->create(['hash' => 'hash1']);
    $employee2 = Employee::factory()->create(['hash' => 'hash2']);

    $this->get(route('anon.logout', ['hash' => 'hash1']))
        ->assertRedirect(route('homepage'));

    $employee1->refresh();
    $employee2->refresh();

    expect($employee1->hash)->not->toBe('hash1');
    expect($employee2->hash)->toBe('hash2');
});

test('logout generates a new random hash', function () {
    $employee = Employee::factory()->create(['hash' => 'old_hash']);

    $this->get(route('anon.logout', ['hash' => 'old_hash']))
        ->assertRedirect(route('homepage'));

    $employee->refresh();
    expect($employee->hash)->not->toBe('old_hash');
    expect(Str::length($employee->hash))->toBe(16); // Default length of Str::random()
});

test('multiple logouts generate different hashes', function () {
    $employee = Employee::factory()->create(['hash' => 'initial_hash']);

    $this->get(route('anon.logout', ['hash' => 'initial_hash']))
        ->assertRedirect(route('homepage'));

    $firstLogoutHash = $employee->fresh()->hash;

    $this->get(route('anon.logout', ['hash' => $firstLogoutHash]))
        ->assertRedirect(route('homepage'));

    $secondLogoutHash = $employee->fresh()->hash;

    expect($firstLogoutHash)->not->toBe('initial_hash');
    expect($secondLogoutHash)->not->toBe($firstLogoutHash);
    expect($secondLogoutHash)->not->toBe('initial_hash');
});
