<?php

use Illuminate\Support\Facades\RateLimiter;

// ── Rate limiter keys are registered ──────────────────────────────────────
test('admin-login rate limiter is registered', function () {
    expect(RateLimiter::limiter('admin-login'))->not->toBeNull();
});

test('student-login rate limiter is registered', function () {
    expect(RateLimiter::limiter('student-login'))->not->toBeNull();
});

test('forgot-password rate limiter is registered', function () {
    expect(RateLimiter::limiter('forgot-password'))->not->toBeNull();
});

// ── Rate limiter hit/clear mechanics ──────────────────────────────────────
test('rate limiter tracks attempts correctly', function () {
    $key = 'test-limiter-' . uniqid();

    expect(RateLimiter::tooManyAttempts($key, 3))->toBeFalse();

    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);

    expect(RateLimiter::tooManyAttempts($key, 3))->toBeTrue();
});

test('rate limiter clears correctly', function () {
    $key = 'test-limiter-clear-' . uniqid();

    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);

    expect(RateLimiter::tooManyAttempts($key, 3))->toBeTrue();

    RateLimiter::clear($key);

    expect(RateLimiter::tooManyAttempts($key, 3))->toBeFalse();
});
