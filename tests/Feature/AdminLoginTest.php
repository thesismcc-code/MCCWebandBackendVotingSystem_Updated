<?php

use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('admin-login');
});

// ── Admin login page loads ─────────────────────────────────────────────────
test('admin login page loads successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Fingerprint Voting System');
});

// ── Validation: missing fields ─────────────────────────────────────────────
test('admin login fails with missing email', function () {
    $response = $this->post('/login', [
        'email'    => '',
        'password' => 'password123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin login fails with missing password', function () {
    $response = $this->post('/login', [
        'email'    => 'admin@test.com',
        'password' => '',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin login fails with invalid email format', function () {
    $response = $this->post('/login', [
        'email'    => 'not-an-email',
        'password' => 'password123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

// ── Wrong credentials ──────────────────────────────────────────────────────
test('admin login fails with wrong credentials', function () {
    $response = $this->post('/login', [
        'email'    => 'wrong@example.com',
        'password' => 'wrongpassword',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

// ── Rate limiting ──────────────────────────────────────────────────────────
test('admin login is rate limited after 5 attempts', function () {
    // Make 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);
    }

    // 6th attempt should be throttled
    $response = $this->post('/login', [
        'email'    => 'admin@test.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', fn($msg) =>
        str_contains($msg, 'Too many login attempts')
    );
});
