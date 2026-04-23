<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Contract\Database;
use App\Domain\User\UserRepository;

class VotingLogsController extends Controller
{
    public function __construct(
        private Database $db,
        private UserRepository $userRepository
    ) {}

    public function index(): View
    {
        $votesSnap = $this->db->getReference('votes')->getSnapshot();
        $usersSnap = $this->db->getReference('users')->getSnapshot();
        $electionsSnap = $this->db->getReference('elections')->getSnapshot();

        $users     = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];
        $elections = $electionsSnap->exists() ? ($electionsSnap->getValue() ?? []) : [];

        // Find active election
        $activeElectionId = null;
        foreach ($elections as $id => $e) {
            if (($e['status'] ?? '') === 'active') { $activeElectionId = $id; break; }
        }

        $logs = [];
        if ($votesSnap->exists() && $votesSnap->getValue()) {
            // Collect unique voters (one log entry per voter)
            $seen = [];
            foreach ($votesSnap->getValue() as $vote) {
                $voterId    = $vote['voter_id']    ?? null;
                $electionId = $vote['election_id'] ?? null;
                if (!$voterId || isset($seen[$voterId . $electionId])) continue;
                $seen[$voterId . $electionId] = true;

                $user      = $users[$voterId] ?? [];
                $studentId = $user['student_id'] ?? '—';
                $name      = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Unknown';
                $enrollYear = null;
                if ($studentId && str_contains($studentId, '-')) {
                    $parts = explode('-', $studentId);
                    $enrollYear = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
                }
                $currentYear = (int) date('Y');
                $yearLevel = $enrollYear ? match(true) {
                    ($currentYear - $enrollYear + 1) === 1 => '1st Year',
                    ($currentYear - $enrollYear + 1) === 2 => '2nd Year',
                    ($currentYear - $enrollYear + 1) === 3 => '3rd Year',
                    ($currentYear - $enrollYear + 1) === 4 => '4th Year',
                    default => 'Alumni',
                } : '—';

                // Determine course from student ID
                $course = '—';
                if (str_starts_with($studentId, 'CS-')) {
                    $course = 'Computer Science';
                } elseif (str_starts_with($studentId, 'IT-')) {
                    $course = 'Information Technology';
                } elseif (str_starts_with($studentId, 'BA-')) {
                    $course = 'Business Administration';
                }

                $logs[] = [
                    'student_id'  => $studentId,
                    'name'        => $name,
                    'course'      => $course,
                    'year_level'  => $yearLevel,
                    'voted_at'    => $vote['created_at'] ?? null,
                    'election_id' => $electionId,
                ];
            }
        }

        // Sort by voted_at desc
        usort($logs, fn($a, $b) => strcmp($b['voted_at'] ?? '', $a['voted_at'] ?? ''));

        return view('votinglogs', compact('logs', 'activeElectionId'));
    }

    public function exportPdf(): View
    {
        // Same logic as index but return a print-friendly view
        $votesSnap = $this->db->getReference('votes')->getSnapshot();
        $usersSnap = $this->db->getReference('users')->getSnapshot();

        $users = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];
        $logs = [];
        
        if ($votesSnap->exists() && $votesSnap->getValue()) {
            $seen = [];
            foreach ($votesSnap->getValue() as $vote) {
                $voterId = $vote['voter_id'] ?? null;
                $electionId = $vote['election_id'] ?? null;
                if (!$voterId || isset($seen[$voterId . $electionId])) continue;
                $seen[$voterId . $electionId] = true;

                $user = $users[$voterId] ?? [];
                $studentId = $user['student_id'] ?? '—';
                $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Unknown';
                
                $enrollYear = null;
                if ($studentId && str_contains($studentId, '-')) {
                    $parts = explode('-', $studentId);
                    $enrollYear = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
                }
                $currentYear = (int) date('Y');
                $yearLevel = $enrollYear ? match(true) {
                    ($currentYear - $enrollYear + 1) === 1 => '1st Year',
                    ($currentYear - $enrollYear + 1) === 2 => '2nd Year',
                    ($currentYear - $enrollYear + 1) === 3 => '3rd Year',
                    ($currentYear - $enrollYear + 1) === 4 => '4th Year',
                    default => 'Alumni',
                } : '—';

                $course = '—';
                if (str_starts_with($studentId, 'CS-')) {
                    $course = 'Computer Science';
                } elseif (str_starts_with($studentId, 'IT-')) {
                    $course = 'Information Technology';
                } elseif (str_starts_with($studentId, 'BA-')) {
                    $course = 'Business Administration';
                }

                $logs[] = [
                    'student_id' => $studentId,
                    'name' => $name,
                    'course' => $course,
                    'year_level' => $yearLevel,
                    'voted_at' => $vote['created_at'] ?? null,
                ];
            }
        }

        usort($logs, fn($a, $b) => strcmp($b['voted_at'] ?? '', $a['voted_at'] ?? ''));

        return view('votinglogs-pdf', compact('logs'));
    }
}
