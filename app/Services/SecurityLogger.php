<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;
use Illuminate\Http\Request;

class SecurityLogger
{
    public function __construct(
        private Database $db
    ) {}

    /**
     * Log general system activity
     */
    public function log(string $level, string $message, string $user = 'System', ?array $context = null): void
    {
        $this->db->getReference('system_logs')->push([
            'level' => $level,
            'message' => $message,
            'user' => $user,
            'context' => $context,
            'created_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log a duplicate vote attempt
     */
    public function logDuplicateVoteAttempt(string $studentId, string $name, ?string $userId = null): void
    {
        $this->logSecurityEvent([
            'type' => 'duplicate_vote_attempt',
            'student_id' => $studentId,
            'name' => $name,
            'user_id' => $userId,
            'description' => 'Student attempted to vote multiple times',
            'severity' => 'high',
            'first_attempt' => now()->toDateTimeLocalString(),
            'second_attempt' => now()->toDateTimeLocalString(),
        ]);
    }

    /**
     * Log a rejected fingerprint scan
     */
    public function logRejectedFingerprint(string $studentId, string $name, string $reason = 'Poor quality scan', ?string $userId = null): void
    {
        $this->logSecurityEvent([
            'type' => 'rejected_fingerprint',
            'student_id' => $studentId,
            'name' => $name,
            'user_id' => $userId,
            'description' => "Fingerprint scan rejected: {$reason}",
            'severity' => 'medium',
            'first_attempt' => now()->toDateTimeLocalString(),
        ]);
    }

    /**
     * Log a denied access attempt
     */
    public function logDeniedAccess(string $studentId, string $name, string $reason = 'Not eligible to vote', ?string $userId = null): void
    {
        $this->logSecurityEvent([
            'type' => 'denied_access',
            'student_id' => $studentId,
            'name' => $name,
            'user_id' => $userId,
            'description' => "Access denied: {$reason}",
            'severity' => 'medium',
            'first_attempt' => now()->toDateTimeLocalString(),
        ]);
    }

    /**
     * Log a failed login attempt
     */
    public function logFailedLogin(string $email, string $reason = 'Invalid credentials', ?Request $request = null): void
    {
        $this->logSecurityEvent([
            'type' => 'failed_login',
            'email' => $email,
            'student_id' => null,
            'name' => $email,
            'description' => "Login failed: {$reason}",
            'severity' => 'medium',
            'first_attempt' => now()->toDateTimeLocalString(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    /**
     * Log a suspicious activity
     */
    public function logSuspiciousActivity(string $activity, array $details = [], ?Request $request = null): void
    {
        $this->logSecurityEvent([
            'type' => 'suspicious_activity',
            'description' => $activity,
            'details' => $details,
            'severity' => 'high',
            'first_attempt' => now()->toDateTimeLocalString(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    /**
     * Private method to log security events to Firebase
     */
    private function logSecurityEvent(array $eventData): void
    {
        $eventData = array_merge($eventData, [
            'id' => 'SEC_' . uniqid(),
            'created_at' => now()->toDateTimeLocalString(),
            'updated_at' => now()->toDateTimeLocalString(),
        ]);

        $this->db->getReference('security_logs')->push($eventData);
    }

    /**
     * Get security statistics
     */
    public function getSecurityStats(): array
    {
        $securityLogsSnap = $this->db->getReference('security_logs')->getSnapshot();
        
        if (!$securityLogsSnap->exists()) {
            return [
                'duplicate_vote_attempts' => 0,
                'rejected_fingerprint_scans' => 0,
                'denied_access_attempts' => 0,
                'failed_login_attempts' => 0,
                'suspicious_activities' => 0,
                'total_incidents' => 0,
            ];
        }

        $logs = $securityLogsSnap->getValue() ?? [];
        $stats = [
            'duplicate_vote_attempts' => 0,
            'rejected_fingerprint_scans' => 0,
            'denied_access_attempts' => 0,
            'failed_login_attempts' => 0,
            'suspicious_activities' => 0,
        ];

        foreach ($logs as $log) {
            $type = $log['type'] ?? 'unknown';
            switch ($type) {
                case 'duplicate_vote_attempt':
                    $stats['duplicate_vote_attempts']++;
                    break;
                case 'rejected_fingerprint':
                    $stats['rejected_fingerprint_scans']++;
                    break;
                case 'denied_access':
                    $stats['denied_access_attempts']++;
                    break;
                case 'failed_login':
                    $stats['failed_login_attempts']++;
                    break;
                case 'suspicious_activity':
                    $stats['suspicious_activities']++;
                    break;
            }
        }

        $stats['total_incidents'] = array_sum($stats);

        return $stats;
    }
}