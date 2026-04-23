<?php

use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('student-login');
});

// ── Student login page loads ───────────────────────────────────────────────
test('student login page loads successfully', function () {
    $response = $this->get('/students');
    $response->assertStatus(200);
    $response->assertSee('Fingerprint Voting System');
});

test('student login page shows old student form by default', function () {
    $response = $this->get('/students');
    $response->assertSee('Old Student');
    $response->assertSee('New Student');
    $response->assertSee('form-old-student');
});

// ── Old student: validation ────────────────────────────────────────────────
test('old student login fails with missing student id', function () {
    $response = $this->post('/validate-login', [
        'student_type' => 'Old Student',
        'student_id'   => '',
        'password'     => 'password123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('old student login fails with missing password', function () {
    $response = $this->post('/validate-login', [
        'student_type' => 'Old Student',
        'student_id'   => 'STU-001-001',
        'password'     => '',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('old student login fails with wrong credentials', function () {
    $response = $this->post('/validate-login', [
        'student_type' => 'Old Student',
        'student_id'   => 'STU-999-999',
        'password'     => 'wrongpassword',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

// ── New student signup: validation ────────────────────────────────────────
test('new student signup fails with missing student id', function () {
    $response = $this->post('/student-validate-login', [
        'student_type'          => 'New Student',
        'student_id'            => '',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('new student signup fails with missing password', function () {
    $response = $this->post('/student-validate-login', [
        'student_type'          => 'New Student',
        'student_id'            => 'STU-001-001',
        'password'              => '',
        'password_confirmation' => '',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('new student signup fails with non-existent student id', function () {
    $response = $this->post('/student-validate-login', [
        'student_type'          => 'New Student',
        'student_id'            => 'STU-000-000',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

// ── Rate limiting ──────────────────────────────────────────────────────────
test('student login is rate limited after 8 attempts', function () {
    for ($i = 0; $i < 8; $i++) {
        $this->post('/validate-login', [
            'student_type' => 'Old Student',
            'student_id'   => 'STU-999-999',
            'password'     => 'wrongpassword',
        ]);
    }

    $response = $this->post('/validate-login', [
        'student_type' => 'Old Student',
        'student_id'   => 'STU-999-999',
        'password'     => 'wrongpassword',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', fn($msg) =>
        str_contains($msg, 'Too many login attempts')
    );
});
