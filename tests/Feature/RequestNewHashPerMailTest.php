<?php

use App\Mail\NewHash;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\post;

beforeEach(function (): void {
    Mail::fake();
});

test('request new hash with valid email', function (): void {
    $employee = Employee::factory()->create(['email' => 'test@asklepios.com']);

    post(route('anon.newHash'), [
        'email' => 'test@asklepios.com',
    ])
        ->assertRedirect(route('homepage'))
        ->assertSessionHas('info', 'Der neue Zugriffscode wurde an test@asklepios.com gesendet.');

    $this->assertDatabaseHas('employees', [
        'id' => $employee->id,
        'email' => 'test@asklepios.com',
    ]);

    $updatedEmployee = Employee::find($employee->id);
    expect($employee->hash)->not->toBe($updatedEmployee->hash);

    Mail::assertQueued(NewHash::class, fn ($mail) => $mail->hasTo($updatedEmployee->email));
});

test('request new hash with valid email without domain', function (): void {
    $employee = Employee::factory()->create(['email' => 'test@asklepios.com']);

    post(route('anon.newHash'), [
        'email' => 'test',
    ])
        ->assertRedirect(route('homepage'))
        ->assertSessionHas('info', 'Der neue Zugriffscode wurde an test@asklepios.com gesendet.');

    Mail::assertQueued(NewHash::class, fn ($mail) => $mail->hasTo($employee->email));
});

test('request new hash with invalid email', function (): void {
    post(route('anon.newHash'), [
        'email' => 'nonexistent@asklepios.com',
    ])
        ->assertRedirect(route('homepage'))
        ->assertSessionHas('warning', 'Die E-Mail nonexistent@asklepios.com wurde nicht gefunden.');

    Mail::assertNothingQueued();
});

test('request new hash generates valid url', function (): void {
    $employee = Employee::factory()->create(['email' => 'test@asklepios.com']);

    post(route('anon.newHash'), [
        'email' => 'test@asklepios.com',
    ]);

    Mail::assertQueued(NewHash::class, function ($mail): true {
        $url = $mail->url;
        expect($url)->toContain(Carbon::now()->yearIso);
        expect($url)->toContain(Employee::where('email', 'test@asklepios.com')->first()->hash);

        return true;
    });
});
