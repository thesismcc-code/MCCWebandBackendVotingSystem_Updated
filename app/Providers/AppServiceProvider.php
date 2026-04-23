<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Domain\Auth\AuthRepository;
use App\Eloquent\Auth\EloquentAuthRepository;
use App\Domain\User\UserRepository;
use App\Eloquent\User\EloquentUserRepository;
use App\Domain\Votes\VotesRepository;
use App\Eloquent\Vote\EloquentVoteRepository;
use App\Domain\Candidates\CandidatesRepository;
use App\Eloquent\Candidates\EloquentCandidateRepository;
use App\Services\ZktecoService;
use App\Services\SecurityLogger;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(AuthRepository::class, EloquentAuthRepository::class);
        $this->app->bind(VotesRepository::class, EloquentVoteRepository::class);
        $this->app->bind(CandidatesRepository::class, EloquentCandidateRepository::class);
        $this->app->singleton(ZktecoService::class, fn() => new ZktecoService());
        $this->app->singleton(SecurityLogger::class, SecurityLogger::class);
    }

    public function boot(): void
    {
        // ── Rate limiters ──────────────────────────────────────────────────────

        // Admin login: 5 attempts per minute per IP
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'Too many login attempts. Please wait a minute and try again.');
                });
        });

        // Student login: 8 attempts per minute per IP
        RateLimiter::for('student-login', function (Request $request) {
            return Limit::perMinute(8)
                ->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'Too many login attempts. Please wait a minute and try again.');
                });
        });

        // Forgot password: 3 requests per 10 minutes per IP (prevent email spam)
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinutes(10, 3)
                ->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'Too many password reset requests. Please wait 10 minutes and try again.');
                });
        });
    }
}
