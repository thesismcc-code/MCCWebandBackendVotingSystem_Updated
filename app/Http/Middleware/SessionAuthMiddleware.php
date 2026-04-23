<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SessionAuthMiddleware
{
    private const ROLE_ROUTES = [
        'admin'   => [
            'dashboard',
            'quick-access',
            'manage-accounts',
            'store-new-accounts',
            'finger-print',
            'fingerprint',
            'voting-logs',
            'security-logs',
            'election-control',
            'system-activity',
            'reports-and-analytics',
            'student-eligibility',
            'logout',
        ],
        'sao' => [
            'sao-dashboard',
            'sao-candidate-list',
            'sao-voter-participation',
            'sao-final-results',
            'sao-publish-results',
            'security-logs',
            'logout',
        ],
        'comelec' => [
            'comelec-dashboard',
            'comelec-manage-candidates',
            'comelec-candidates',
            'security-logs',
            'logout',
        ],
        'student' => [
            'students-dashboard',
            'students-profile',
            'students-update-profile',
            'students-verification',
            'students-verify-otp',
            'students-resend-otp',
            'students-validate-verification',
            'students-tutorials',
            'students-how-to-vote',
            'students-results',
            'student-logout',
        ],
    ];

    private const ROLE_HOME = [
        'admin'   => 'view.dashboard',
        'sao'     => 'view.sao-dashboard',
        'comelec' => 'view.comelec-dashboard',
        'student' => 'view.student-dashboard',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('auth_user')) {
            Log::info('Invalid access');
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access this page.');
        }

        $role        = Session::get('auth_user.role');
        $currentPath = $request->path();

        Log::info('Middleware check', [
            'role'         => $role,
            'current_path' => $currentPath,
            'can_access'   => $this->canAccess($role, $currentPath),
            'role_home'    => self::ROLE_HOME[$role] ?? 'NOT FOUND',
            'auth_user'    => Session::get('auth_user'),
        ]);

        if (!$this->canAccess($role, $currentPath)) {
            $home = self::ROLE_HOME[$role] ?? 'login';
            return redirect()->route($home)
                ->with('error', 'You are not authorized to access that page.');
        }

        return $next($request);
    }

    private function canAccess(string $role, string $path): bool
    {
        $allowedPaths = self::ROLE_ROUTES[$role] ?? [];

        foreach ($allowedPaths as $allowed) {
            if (str_starts_with($path, $allowed)) {
                return true;
            }
        }

        return false;
    }
}
