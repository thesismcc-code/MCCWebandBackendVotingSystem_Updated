<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('forgot-password');
    Mail::fake();
});

// ── Forgot password page ───────────────────────────────────────────────────
test('forgot password page loads successfully', function () {
    $response = $this->get('/forgot-password');
    $response->assertStatus(200);
    $response->assertSee('Forgot Password');
});

// ── Validation ────────────────────────────────────────────────────────────
test('forgot password fails with missing email', function () {
    $response = $this->post('/forgot-password', ['email' => '']);
    $response->assertRedirect();
    // Laravel validation redirects back with errors
    $response->assertSessionHasErrors('email');
});

test('forgot password fails with invalid email format', function () {
    $response = $this->post('/forgot-password', ['email' => 'not-an-email']);
    $response->assertRedirect();
    $response->assertSessionHasErrors('email');
});

// ── Non-existent email shows generic success (no enumeration) ─────────────
test('forgot password shows success message for unknown email', function () {
    $response = $this->post('/forgot-password', [
        'email' => 'doesnotexist@example.com',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('status');
});

// ── Rate limiting ──────────────────────────────────────────────────────────
test('forgot password is rate limited after 3 attempts in 10 minutes', function () {
    for ($i = 0; $i < 3; $i++) {
        $this->post('/forgot-password', ['email' => 'test@example.com']);
    }

    $response = $this->post('/forgot-password', ['email' => 'test@example.com']);
    $response->assertRedirect();
    $response->assertSessionHas('error', fn($msg) =>
        str_contains($msg, 'Too many password reset requests')
    );
});

// ── Reset password page: invalid token ────────────────────────────────────
test('reset password page rejects invalid token', function () {
    $response = $this->get('/reset-password?token=invalidtoken&email=test@example.com');
    $response->assertRedirect('/');
    $response->assertSessionHas('error');
});

test('reset password page rejects missing token', function () {
    $response = $this->get('/reset-password');
    $response->assertRedirect('/');
    $response->assertSessionHas('error');
});

// ── Reset password: valid token flow ──────────────────────────────────────
test('reset password page loads with valid token', function () {
    $token = \Illuminate\Support\Str::random(64);
    Cache::put('pwd_reset_' . $token, 'test@example.com', now()->addMinutes(60));

    $response = $this->get('/reset-password?token=' . $token . '&email=test@example.com');
    $response->assertStatus(200);
    $response->assertSee('Set New Password');
});

test('reset password fails with mismatched passwords', function () {
    $token = \Illuminate\Support\Str::random(64);
    Cache::put('pwd_reset_' . $token, 'test@example.com', now()->addMinutes(60));

    $response = $this->post('/reset-password', [
        'token'                 => $token,
        'email'                 => 'test@example.com',
        'password'              => 'newpassword123',
        'password_confirmation' => 'differentpassword',
    ]);
    $response->assertRedirect();
    $response->assertSessionHasErrors('password');
});

test('reset password fails with expired token', function () {
    $response = $this->post('/reset-password', [
        'token'                 => 'expiredtoken',
        'email'                 => 'test@example.com',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('reset password fails with short password', function () {
    $token = \Illuminate\Support\Str::random(64);
    Cache::put('pwd_reset_' . $token, 'test@example.com', now()->addMinutes(60));

    $response = $this->post('/reset-password', [
        'token'                 => $token,
        'email'                 => 'test@example.com',
        'password'              => 'short',
        'password_confirmation' => 'short',
    ]);
    $response->assertRedirect();
    $response->assertSessionHasErrors('password');
});
