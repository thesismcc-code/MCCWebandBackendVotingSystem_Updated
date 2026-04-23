<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Kreait\Firebase\Contract\Database;

use App\Application\RegisterUser\RegisterUser;
use App\Application\RegisterVotes\RegisterVotes;
use App\Application\RegisterCandidates\RegisterCandidates;


class DashboardController extends Controller
{
    private RegisterUser $registerUser;
    private RegisterVotes $registerVote;
    private RegisterCandidates $registerCandidates;
    private Database $db;

    public function __construct(
        RegisterUser $registerUser,
        RegisterVotes $registerVote,
        RegisterCandidates $registerCandidates,
        Database $db
    ) {
        $this->registerUser       = $registerUser;
        $this->registerVote       = $registerVote;
        $this->registerCandidates = $registerCandidates;
        $this->db                 = $db;
    }

    private function getActiveElection(): array
    {
        $electionsRef = $this->db->getReference('elections');
        $snapshot = $electionsRef->orderByChild('status')->equalTo('active')->getSnapshot();
        if ($snapshot->exists() && $snapshot->getValue()) {
            $elections = $snapshot->getValue();
            $key = array_key_first($elections);
            if ($key) return array_merge(['id' => $key], $elections[$key]);
        }
        $all = $electionsRef->getSnapshot();
        if ($all->exists() && $all->getValue()) {
            foreach ($all->getValue() as $id => $e) {
                if (in_array($e['status'] ?? '', ['upcoming', 'active'])) {
                    return array_merge(['id' => $id], $e);
                }
            }
        }
        return [];
    }

    public function index(): View
    {
        $data = [
            "stats_card_data" => [
                "total_register_voters" => $this->registerUser->total_registered_voters(),
                "live_vote_cast"        => $this->registerVote->liveVoteCast(),
                "running_candidates"    => $this->registerCandidates->getRunnCandidatesCount(),
                "turn_out_rates"        => $this->registerUser->turnOutRates(),
            ],
            "live_candidate_result"  => $this->registerVote->liveCandidateResult(),
            "realtime_turnout"       => $this->registerUser->realtimeVoterTurnout(),
            "per_year_level_turnout" => $this->registerUser->voterTurnoutByYearLevel(),
        ];

        $activeElection = $this->getActiveElection();
        $recentActivity = $this->getRecentActivity();
        $notYetVoted    = $data['realtime_turnout']['not_yet_voted'] ?? 0;

        return view('dashboard', compact('data', 'activeElection', 'recentActivity', 'notYetVoted'));
    }

    private function getRecentActivity(): array
    {
        // Get last 10 votes from voting logs
        $votesSnap = $this->db->getReference('votes')->getSnapshot();
        $usersSnap = $this->db->getReference('users')->getSnapshot();
        
        $users = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];
        $activities = [];

        if ($votesSnap->exists() && $votesSnap->getValue()) {
            $votes = $votesSnap->getValue();
            // Sort by created_at desc
            usort($votes, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
            
            // Get unique voters (one entry per voter)
            $seen = [];
            foreach ($votes as $vote) {
                $voterId = $vote['voter_id'] ?? null;
                if (!$voterId || isset($seen[$voterId])) continue;
                $seen[$voterId] = true;

                $user = $users[$voterId] ?? [];
                $yearLevel = $user['year_level'] ?? $user['Year Level'] ?? $user['year'] ?? 'Unknown';
                $course = $user['course'] ?? $user['Course'] ?? 'Unknown';
                
                $activities[] = [
                    'type' => 'vote',
                    'message' => "Student from {$yearLevel} {$course} voted",
                    'time' => $vote['created_at'] ?? now()->toIso8601String(),
                ];

                if (count($activities) >= 10) break;
            }
        }

        // Add system logs
        $logsSnap = $this->db->getReference('system_logs')->getSnapshot();
        if ($logsSnap->exists() && $logsSnap->getValue()) {
            $logs = $logsSnap->getValue();
            usort($logs, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
            
            foreach (array_slice($logs, 0, 5) as $log) {
                $activities[] = [
                    'type' => $log['level'] ?? 'info',
                    'message' => $log['message'] ?? 'System activity',
                    'time' => $log['created_at'] ?? now()->toIso8601String(),
                    'user' => $log['user'] ?? 'System',
                ];
            }
        }

        // Sort all activities by time desc
        usort($activities, fn($a, $b) => strcmp($b['time'], $a['time']));

        // Add human-readable time_ago for JSON responses
        foreach ($activities as &$act) {
            try {
                $act['time_ago'] = \Carbon\Carbon::parse($act['time'])->diffForHumans();
            } catch (\Exception $e) {
                $act['time_ago'] = 'recently';
            }
        }

        return array_slice($activities, 0, 10);
    }

    public function exportVoterListCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Get active election voters
        $votesSnap = $this->db->getReference('votes')->getSnapshot();
        $usersSnap = $this->db->getReference('users')->getSnapshot();

        $users  = $usersSnap->exists()  ? ($usersSnap->getValue()  ?? []) : [];
        $votes  = $votesSnap->exists()  ? ($votesSnap->getValue()  ?? []) : [];

        // Get active election ID
        $activeElectionId = null;
        $elSnap = $this->db->getReference('elections')->orderByChild('status')->equalTo('active')->getSnapshot();
        if ($elSnap->exists() && $elSnap->getValue()) {
            $activeElectionId = array_key_first($elSnap->getValue());
        }

        // Build voted set
        $votedSet = [];
        foreach ($votes as $v) {
            if ($activeElectionId && ($v['election_id'] ?? '') !== $activeElectionId) continue;
            $votedSet[$v['voter_id'] ?? ''] = true;
        }

        // Build student rows
        $rows = [['Student ID', 'Full Name', 'Course', 'Year Level', 'Email', 'Voted', 'Email Verified']];
        foreach ($users as $uid => $u) {
            if (($u['role'] ?? '') !== 'student') continue;
            $name = trim(($u['first_name'] ?? '') . ' ' . ($u['middle_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
            $rows[] = [
                $u['student_id']       ?? '—',
                $name                  ?: 'Unknown',
                $u['course']           ?? '—',
                $u['year_level']       ?? $u['year'] ?? '—',
                $u['email']            ?? '—',
                isset($votedSet[$uid]) ? 'Yes' : 'No',
                !empty($u['email_verified_at']) ? 'Yes' : 'No',
            ];
        }

        $filename = 'mcc-voter-list-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function refresh(): JsonResponse
    {
        $turnout    = $this->registerUser->realtimeVoterTurnout();
        $yearLevels = $this->registerUser->voterTurnoutByYearLevel();
        $candidates = $this->registerVote->liveCandidateResult();
        $election   = $this->getActiveElection();

        return response()->json([
            'stats' => [
                'total_register_voters' => $this->registerUser->total_registered_voters(),
                'live_vote_cast'        => $this->registerVote->liveVoteCast(),
                'running_candidates'    => $this->registerCandidates->getRunnCandidatesCount(),
                'turnout_percent'       => $this->registerUser->turnOutRates()['turnout_percent'] ?? 0,
            ],
            'realtime_turnout'       => $turnout,
            'per_year_level_turnout' => $yearLevels,
            'live_candidate_result'  => $candidates,
            'active_election'        => $election,
            'recent_activity'        => $this->getRecentActivity(),
            'not_yet_voted'          => $turnout['not_yet_voted'] ?? 0,
            'refreshed_at'           => now()->format('h:i:s A'),
        ]);
    }
}
