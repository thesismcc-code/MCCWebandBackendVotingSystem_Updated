<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class SystemActivityController extends Controller
{
    public function __construct(private Database $db) {}

    public function index(Request $request): View
    {
        $logsSnap = $this->db->getReference('system_logs')->getSnapshot();
        $allLogs  = [];

        if ($logsSnap->exists() && $logsSnap->getValue()) {
            foreach ($logsSnap->getValue() as $logId => $log) {
                $log['id'] = $logId;
                $allLogs[] = $log;
            }
            // Sort by created_at descending (newest first)
            usort($allLogs, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
        }

        // Apply filters
        $userFilter = $request->get('user', 'all');
        $dateFilter = $request->get('date', 'all');

        // Filter by user
        if ($userFilter !== 'all') {
            $allLogs = array_filter($allLogs, function($log) use ($userFilter) {
                $logUser = strtolower($log['user'] ?? '');
                return str_contains($logUser, strtolower($userFilter));
            });
        }

        // Filter by date
        if ($dateFilter !== 'all') {
            $allLogs = array_filter($allLogs, function($log) use ($dateFilter) {
                $logDate = $log['created_at'] ?? '';
                if (!$logDate) return false;

                try {
                    $logCarbon = Carbon::parse($logDate);
                    $now = Carbon::now();

                    switch ($dateFilter) {
                        case 'today':
                            return $logCarbon->isToday();
                        case 'yesterday':
                            return $logCarbon->isYesterday();
                        case 'last_week':
                            return $logCarbon->isAfter($now->copy()->subWeek());
                        case 'last_month':
                            return $logCarbon->isAfter($now->copy()->subMonth());
                        default:
                            return true;
                    }
                } catch (\Exception $e) {
                    return false;
                }
            });
        }

        // Separate real-time logs and error logs
        $realTimeLogs = array_filter($allLogs, fn($l) => !in_array($l['level'] ?? '', ['error', 'critical']));
        $errorLogs    = array_filter($allLogs, fn($l) => in_array($l['level'] ?? '', ['error', 'critical']));

        // Get unique users for filter dropdown
        $users = [];
        foreach ($allLogs as $log) {
            $user = $log['user'] ?? 'Unknown';
            if (!in_array($user, $users)) {
                $users[] = $user;
            }
        }
        sort($users);

        return view('systemactivity', [
            'realTimeLogs' => array_values($realTimeLogs),
            'errorLogs'    => array_values($errorLogs),
            'users'        => $users,
            'currentUserFilter' => $userFilter,
            'currentDateFilter' => $dateFilter,
        ]);
    }

    public function log(Request $request)
    {
        $request->validate(['level' => 'required|string', 'message' => 'required|string']);
        $this->db->getReference('system_logs')->push([
            'level'      => $request->level,
            'message'    => $request->message,
            'user'       => $request->user_role ?? 'System',
            'context'    => $request->context ?? null,
            'created_at' => now()->toIso8601String(),
        ]);
        return response()->json(['status' => 'logged']);
    }
}
