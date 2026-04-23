<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Contract\Database;

class StudentDashboardController extends Controller
{
    public function __construct(private Database $db) {}

    public function index(): View
    {
        $allSnap          = $this->db->getReference('elections')->getSnapshot();
        $resultsPublished = false;
        $electionName     = null;
        $activeElectionId = null;
        $activeElection   = null;
        $isActive         = false;

        if ($allSnap->exists() && $allSnap->getValue()) {
            foreach ($allSnap->getValue() as $eid => $edata) {
                // Published results take priority
                if (!empty($edata['results_published'])) {
                    $resultsPublished = true;
                    $electionName     = $edata['election_name'] ?? 'Election';
                }
                // Active election
                if (($edata['status'] ?? '') === 'active') {
                    $activeElectionId = $eid;
                    $activeElection   = $edata;
                    $isActive         = true;
                    $electionName     = $edata['election_name'] ?? 'Election';
                }
            }
        }

        // Live vote counts per position (only when active)
        $liveResults  = [];
        $totalVoters  = 0;
        $votedCount   = 0;
        $hasVoted     = false;

        if ($activeElectionId) {
            $voterId = Session::get('auth_user.id');

            // Total eligible students
            $usersSnap = $this->db->getReference('users')->getSnapshot();
            if ($usersSnap->exists() && $usersSnap->getValue()) {
                foreach ($usersSnap->getValue() as $u) {
                    if (($u['role'] ?? '') === 'student' && empty($u['is_deleted'])) {
                        $totalVoters++;
                    }
                }
            }

            // Votes for active election
            $votesSnap = $this->db->getReference('votes')
                ->orderByChild('election_id')->equalTo($activeElectionId)->getSnapshot();

            $tally      = [];
            $votersSeen = [];

            if ($votesSnap->exists() && $votesSnap->getValue()) {
                foreach ($votesSnap->getValue() as $v) {
                    $pos  = $v['position']     ?? null;
                    $cand = $v['candidate_id'] ?? null;
                    $vid  = $v['voter_id']     ?? null;

                    if ($pos && $cand) {
                        $tally[$pos][$cand] = ($tally[$pos][$cand] ?? 0) + 1;
                    }
                    if ($vid) {
                        $votersSeen[$vid] = true;
                        if ($vid === $voterId) $hasVoted = true;
                    }
                }
                $votedCount = count($votersSeen);
            }

            // Resolve candidate names
            $candSnap  = $this->db->getReference('candidates')->getSnapshot();
            $usersData = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];

            if ($candSnap->exists() && $candSnap->getValue()) {
                foreach ($candSnap->getValue() as $cid => $c) {
                    if (($c['election_id'] ?? '') !== $activeElectionId) continue;
                    if (($c['status'] ?? '') !== 'approved') continue;

                    $pos  = $c['position_id'] ?? $c['position'] ?? 'Unknown';
                    $uid  = $c['user_id'] ?? $cid;
                    $u    = $usersData[$uid] ?? [];
                    $name = !empty($c['full_name'])
                        ? $c['full_name']
                        : (trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Unknown');

                    $votes = $tally[$pos][$cid] ?? 0;
                    $liveResults[$pos][] = ['name' => $name, 'votes' => $votes, 'id' => $cid];
                }

                // Sort each position by votes desc
                foreach ($liveResults as $pos => &$cands) {
                    usort($cands, fn($a, $b) => $b['votes'] <=> $a['votes']);
                }
                unset($cands);

                // Sort positions
                $order = ['President','Vice President','Secretary','Treasurer','Auditor','PRO'];
                uksort($liveResults, function($a, $b) use ($order) {
                    $ia = array_search($a, $order); $ib = array_search($b, $order);
                    return ($ia === false ? 999 : $ia) <=> ($ib === false ? 999 : $ib);
                });
            }
        }

        $turnoutPct = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100) : 0;

        return view('student.dashboard', compact(
            'resultsPublished', 'electionName', 'isActive',
            'activeElection', 'liveResults', 'totalVoters',
            'votedCount', 'hasVoted', 'turnoutPct'
        ));
    }
}
