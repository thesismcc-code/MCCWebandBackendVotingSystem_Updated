<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Domain\User\UserRepository;
use App\Application\RegisterAuth\RegisterAuth;
use Kreait\Firebase\Contract\Database;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // ── Mock Firebase Database so no real network calls happen ──────────
        $fakeRef = \Mockery::mock(\Kreait\Firebase\Database\Reference::class);
        $fakeRef->shouldReceive('getSnapshot')->andReturn(
            \Mockery::mock(\Kreait\Firebase\Database\Snapshot::class, function ($m) {
                $m->shouldReceive('exists')->andReturn(false)->byDefault();
                $m->shouldReceive('getValue')->andReturn(null)->byDefault();
            })
        )->byDefault();
        $fakeRef->shouldReceive('getValue')->andReturn(null)->byDefault();
        $fakeRef->shouldReceive('getChild')->andReturn($fakeRef)->byDefault();
        $fakeRef->shouldReceive('getReference')->andReturn($fakeRef)->byDefault();
        $fakeRef->shouldReceive('orderByChild')->andReturn($fakeRef)->byDefault();
        $fakeRef->shouldReceive('equalTo')->andReturn($fakeRef)->byDefault();
        $fakeRef->shouldReceive('set')->andReturn(null)->byDefault();
        $fakeRef->shouldReceive('update')->andReturn(null)->byDefault();
        $fakeRef->shouldReceive('push')->andReturn($fakeRef)->byDefault();
        $fakeRef->shouldReceive('getKey')->andReturn('fake-key')->byDefault();

        $fakeDb = \Mockery::mock(Database::class);
        $fakeDb->shouldReceive('getReference')->andReturn($fakeRef)->byDefault();

        $this->app->instance(Database::class, $fakeDb);

        // ── Mock RegisterAuth so login always fails with invalid credentials ─
        $this->app->bind(RegisterAuth::class, function () {
            $mock = \Mockery::mock(RegisterAuth::class);
            $mock->shouldReceive('login')
                 ->andThrow(new \InvalidArgumentException('Invalid email or password.'))
                 ->byDefault();
            $mock->shouldReceive('loginWithStudentID')
                 ->andThrow(new \InvalidArgumentException('Student ID not found.'))
                 ->byDefault();
            $mock->shouldReceive('logout')->andReturn(true)->byDefault();
            return $mock;
        });
    }
}
