<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class SAOVoterParticipationController extends Controller
{
    public function __construct(private Database $db) {}

    public function index(Request $request): View
    {
        $search     = trim($request->input('search', ''));
        $courseFilter = $request->input('course_filter', '');
        $yearFilter   = $request->input('year_filter', '');

        // ── 1. Get active election ────────────────────────────────────
        $electionSnap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();

        $activeElectionId   = null;
        $activeElectionName = 'No Active Election';

        if ($electionSnap->exists() && $electionSnap->getValue()) {
            $activeElectionId   = array_key_first($electionSnap->getValue());
            $activeElectionName = $electionSnap->getValue()[$activeElectionId]['election_name'] ?? 'Active Election';
        }

        // ── 2. Get all votes for active election ──────────────────────
        $voterRows = collect();

        if ($activeElectionId) {
            $votesSnap = $this->db->getReference('votes')
                ->orderByChild('election_id')
                ->equalTo($activeElectionId)
                ->getSnapshot();

            if ($votesSnap->exists() && $votesSnap->getValue()) {
                // Collect unique voter IDs with their earliest vote timestamp
                $voterTimestamps = [];
                foreach ($votesSnap->getValue() as $vote) {
                    $vid = $vote['voter_id'] ?? null;
                    if (!$vid) continue;
                    $ts = $vote['created_at'] ?? null;
                    if (!isset($voterTimestamps[$vid]) || $ts < $voterTimestamps[$vid]) {
                        $voterTimestamps[$vid] = $ts;
                    }
                }

                // ── 3. Fetch user data for each voter ─────────────────
                $usersSnap = $this->db->getReference('users')->getSnapshot();
                $allUsers  = ($usersSnap->exists() && $usersSnap->getValue())
                    ? $usersSnap->getValue()
                    : [];

                foreach ($voterTimestamps as $voterId => $votedAt) {
                    $user = $allUsers[$voterId] ?? null;
                    if (!$user || ($user['role'] ?? '') !== 'student') continue;

                    $fullName  = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                    $course    = $user['course'] ?? '—';
                    $yearLevel = $user['year_level'] ?? '—';
                    $studentId = $user['student_id'] ?? '—';

                    // ── Search filter ──────────────────────────────────
                    if ($search) {
                        $lower = strtolower($search);
                        if (
                            !str_contains(strtolower($fullName), $lower) &&
                            !str_contains(strtolower($studentId), $lower)
                        ) continue;
                    }

                    // ── Course filter ──────────────────────────────────
                    if ($courseFilter && strtolower($course) !== strtolower($courseFilter)) continue;

                    // ── Year level filter ──────────────────────────────
                    if ($yearFilter) {
                        $yearNum = (int) preg_replace('/[^0-9]/', '', (string) $yearLevel);
                        if ($yearNum !== (int) $yearFilter) continue;
                    }

                    $voterRows->push((object) [
                        'student_id' => $studentId,
                        'name'       => $fullName,
                        'course'     => $course,
                        'year_level' => $yearLevel,
                        'voted_at'   => $votedAt,
                    ]);
                }

                // Sort by voted_at descending (most recent first)
                $voterRows = $voterRows->sortByDesc('voted_at')->values();
            }
        }

        // ── 4. Paginate ───────────────────────────────────────────────
        $perPage     = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paged       = new LengthAwarePaginator(
            $voterRows->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $voterRows->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // ── 5. Build unique course list for filter dropdown ───────────
        $allCoursesSnap = $this->db->getReference('users')->getSnapshot();
        $courseOptions  = collect();
        if ($allCoursesSnap->exists() && $allCoursesSnap->getValue()) {
            foreach ($allCoursesSnap->getValue() as $u) {
                if (($u['role'] ?? '') === 'student' && !empty($u['course'])) {
                    $courseOptions->push($u['course']);
                }
            }
            $courseOptions = $courseOptions->unique()->sort()->values();
        }

        return view('saovoterparticipation', [
            'voters'            => $paged,
            'totalVoters'       => $voterRows->count(),
            'activeElectionName'=> $activeElectionName,
            'courseOptions'     => $courseOptions,
            'search'            => $search,
            'courseFilter'      => $courseFilter,
            'yearFilter'        => $yearFilter,
        ]);
    }
}
