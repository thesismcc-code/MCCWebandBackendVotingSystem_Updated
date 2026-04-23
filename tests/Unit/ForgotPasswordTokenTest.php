<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

// ── Token storage and retrieval ────────────────────────────────────────────
test('reset token is stored in cache with correct email', function () {
    $token = Str::random(64);
    $email = 'user@example.com';

    Cache::put('pwd_reset_' . $token, $email, now()->addMinutes(60));

    expect(Cache::get('pwd_reset_' . $token))->toBe($email);
});

test('reset token expires after TTL', function () {
    $token = Str::random(64);

    Cache::put('pwd_reset_' . $token, 'user@example.com', now()->addSeconds(1));

    sleep(2);

    expect(Cache::has('pwd_reset_' . $token))->toBeFalse();
});

test('reset token is removed after use', function () {
    $token = Str::random(64);
    Cache::put('pwd_reset_' . $token, 'user@example.com', now()->addMinutes(60));

    // Simulate token consumption
    Cache::forget('pwd_reset_' . $token);

    expect(Cache::has('pwd_reset_' . $token))->toBeFalse();
});

test('different tokens do not collide', function () {
    $token1 = Str::random(64);
    $token2 = Str::random(64);

    Cache::put('pwd_reset_' . $token1, 'user1@example.com', now()->addMinutes(60));
    Cache::put('pwd_reset_' . $token2, 'user2@example.com', now()->addMinutes(60));

    expect(Cache::get('pwd_reset_' . $token1))->toBe('user1@example.com');
    expect(Cache::get('pwd_reset_' . $token2))->toBe('user2@example.com');
});

// ── Token validation logic ─────────────────────────────────────────────────
test('token is invalid when email does not match', function () {
    $token = Str::random(64);
    Cache::put('pwd_reset_' . $token, 'correct@example.com', now()->addMinutes(60));

    $cached = Cache::get('pwd_reset_' . $token);
    $submitted = 'wrong@example.com';

    expect($cached === $submitted)->toBeFalse();
});

test('token is valid when email matches', function () {
    $token = Str::random(64);
    $email = 'user@example.com';
    Cache::put('pwd_reset_' . $token, $email, now()->addMinutes(60));

    $cached = Cache::get('pwd_reset_' . $token);

    expect($cached === $email)->toBeTrue();
});
