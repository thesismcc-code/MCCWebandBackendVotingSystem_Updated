<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class SecurityLogsController extends Controller
{
    public function __construct(
        private Database $db
    ) {}

    public function index(Request $request): View
    {
        // Get security logs from Firebase
        $securityLogsSnap = $this->db->getReference('security_logs')->getSnapshot();
        $usersSnap = $this->db->getReference('users')->getSnapshot();

        $users = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];
        $securityLogs = $securityLogsSnap->exists() ? ($securityLogsSnap->getValue() ?? []) : [];

        // Get filter parameters
        $searchQuery = $request->input('search', '');
        $courseFilter = $request->input('course', '');
        $yearLevelFilter = $request->input('year_level', '');

        // Process security logs
        $logs = [];
        $duplicateVoteAttempts = 0;
        $rejectedFingerprintScans = 0;
        $deniedAccessAttempts = 0;

        foreach ($securityLogs as $logId => $log) {
            $userId = $log['user_id'] ?? null;
            $user = $userId ? ($users[$userId] ?? []) : [];
            
            $studentId = $user['student_id'] ?? $log['student_id'] ?? '—';
            $name = isset($user['first_name'], $user['last_name']) 
                ? trim($user['first_name'] . ' ' . $user['last_name'])
                : ($log['name'] ?? 'Unknown User');

            // Determine course and year level from student ID
            $course = $this->getCourseFromStudentId($studentId);
            $yearLevel = $this->getYearLevelFromStudentId($studentId);

            $logType = $log['type'] ?? 'unknown';
            
            // Count different types of security events
            switch ($logType) {
                case 'duplicate_vote_attempt':
                    $duplicateVoteAttempts++;
                    break;
                case 'rejected_fingerprint':
                    $rejectedFingerprintScans++;
                    break;
                case 'denied_access':
                    $deniedAccessAttempts++;
                    break;
            }

            $logEntry = [
                'student_id' => $studentId,
                'name' => $name,
                'course' => $course,
                'year_level' => $yearLevel,
                'first_attempt' => $log['first_attempt'] ?? $log['created_at'] ?? null,
                'second_attempt' => $log['second_attempt'] ?? null,
                'status' => $this->getStatusFromType($logType),
                'type' => $logType,
                'description' => $log['description'] ?? '',
                'created_at' => $log['created_at'] ?? null,
            ];

            // Apply filters
            $matchesSearch = empty($searchQuery) || 
                stripos($studentId, $searchQuery) !== false || 
                stripos($name, $searchQuery) !== false;

            $matchesCourse = empty($courseFilter) || 
                $courseFilter === 'All Courses' || 
                $course === $courseFilter;

            $matchesYearLevel = empty($yearLevelFilter) || 
                $yearLevelFilter === 'Year Level' || 
                $yearLevel === $yearLevelFilter;

            if ($matchesSearch && $matchesCourse && $matchesYearLevel) {
                $logs[] = $logEntry;
            }
        }

        // Sort by created_at desc
        usort($logs, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        $stats = [
            'duplicate_vote_attempts' => $duplicateVoteAttempts,
            'rejected_fingerprint_scans' => $rejectedFingerprintScans,
            'denied_access_attempts' => $deniedAccessAttempts,
        ];

        // Get unique courses and year levels for filters
        $courses = array_unique(array_column($logs, 'course'));
        $yearLevels = array_unique(array_column($logs, 'year_level'));
        sort($courses);
        sort($yearLevels);

        return view('security-logs', compact('logs', 'stats', 'courses', 'yearLevels', 'searchQuery', 'courseFilter', 'yearLevelFilter'));
    }

    private function getCourseFromStudentId(string $studentId): string
    {
        if (str_starts_with($studentId, 'CS-')) {
            return 'Computer Science';
        } elseif (str_starts_with($studentId, 'IT-')) {
            return 'Information Technology';
        } elseif (str_starts_with($studentId, 'BA-')) {
            return 'Business Administration';
        } elseif (str_starts_with($studentId, 'ED-')) {
            return 'Education';
        } elseif (str_starts_with($studentId, 'EN-')) {
            return 'Engineering';
        }
        
        return 'Unknown Course';
    }

    private function getYearLevelFromStudentId(string $studentId): string
    {
        if (!str_contains($studentId, '-')) {
            return '—';
        }

        $parts = explode('-', $studentId);
        if (count($parts) < 2 || !is_numeric($parts[1])) {
            return '—';
        }

        $enrollYear = (int)$parts[1];
        $currentYear = (int)date('Y');
        $yearLevel = $currentYear - $enrollYear + 1;

        return match($yearLevel) {
            1 => '1st Year',
            2 => '2nd Year', 
            3 => '3rd Year',
            4 => '4th Year',
            default => 'Alumni',
        };
    }

    private function getStatusFromType(string $type): string
    {
        return match($type) {
            'duplicate_vote_attempt' => 'Blocked',
            'rejected_fingerprint' => 'Rejected',
            'denied_access' => 'Denied',
            default => 'Unknown',
        };
    }
}