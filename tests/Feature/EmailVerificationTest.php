<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    Mail::fake();
});

// ── Verification page requires auth ───────────────────────────────────────
test('verification page redirects unauthenticated users', function () {
    $response = $this->get('/students-verification');
    $response->assertRedirect('/students');
});

test('verification page redirects non-student users', function () {
    Session::put('auth_user', [
        'id'    => 'test-id',
        'email' => 'admin@test.com',
        'role'  => 'admin',
    ]);

    $response = $this->get('/students-verification');
    $response->assertRedirect('/students');
});

// ── OTP verification: missing OTP ─────────────────────────────────────────
test('otp verification fails with missing otp', function () {
    Session::put('auth_user', [
        'id'    => 'student-test-id',
        'email' => 'student@test.com',
        'role'  => 'student',
    ]);

    $response = $this->post('/students-verify-otp', ['otp' => '']);
    $response->assertRedirect();
    $response->assertSessionHasErrors('otp');
});

test('otp verification fails with wrong otp', function () {
    $userId = 'student-test-id';

    Session::put('auth_user', [
        'id'    => $userId,
        'email' => 'student@test.com',
        'role'  => 'student',
    ]);

    // Store a known OTP
    Cache::put('otp_' . $userId, '1234', now()->addMinutes(10));

    $response = $this->post('/students-verify-otp', ['otp' => '9999']);
    $response->assertRedirect();
    $response->assertSessionHasErrors('otp');
});

test('otp verification fails with expired otp', function () {
    $userId = 'student-expired-id';

    Session::put('auth_user', [
        'id'    => $userId,
        'email' => 'student@test.com',
        'role'  => 'student',
    ]);

    // No OTP in cache (simulates expiry)
    Cache::forget('otp_' . $userId);

    $response = $this->post('/students-verify-otp', ['otp' => '1234']);
    $response->assertRedirect();
    $response->assertSessionHasErrors('otp');
});

test('otp must be exactly 4 characters', function () {
    Session::put('auth_user', [
        'id'    => 'student-test-id',
        'email' => 'student@test.com',
        'role'  => 'student',
    ]);

    $response = $this->post('/students-verify-otp', ['otp' => '12']);
    $response->assertRedirect();
    $response->assertSessionHasErrors('otp');
});

// ── Resend OTP: requires auth ──────────────────────────────────────────────
test('resend otp requires student session', function () {
    $response = $this->postJson('/students-resend-otp');
    $response->assertStatus(401);
});

test('resend otp returns success for authenticated student', function () {
    Session::put('auth_user', [
        'id'    => 'student-test-id',
        'email' => 'student@test.com',
        'role'  => 'student',
    ]);

    // Mock the mail send — Mail::fake() is already set
    $response = $this->postJson('/students-resend-otp');

    // Will fail at actual mail send to Firebase email, but the controller
    // logic up to that point should be reachable
    $response->assertStatus(200)->assertJson(['success' => true]);
})->skip('Requires live Firebase connection');
