<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Contract\Database;
use App\Domain\User\UserRepository;

class ReportAndAnalyticsController extends Controller
{
    public function __construct(
        private Database $db,
        private UserRepository $userRepository
    ) {}

    public function index(): View
    {
        $turnout    = $this->userRepository->realtimeVoterTurnout();
        $byYear     = $this->userRepository->voterTurnoutByYearLevel();

        // Count active election positions & candidates
        $electionsSnap  = $this->db->getReference('elections')->orderByChild('status')->equalTo('active')->getSnapshot();
        $activeElectionId = null;
        $electionName     = 'No Active Election';
        if ($electionsSnap->exists() && $electionsSnap->getValue()) {
            $activeElectionId = array_key_first($electionsSnap->getValue());
            $electionName     = $electionsSnap->getValue()[$activeElectionId]['election_name'] ?? $electionName;
        }

        $positions  = 0;
        $candidates = 0;
        if ($activeElectionId) {
            $candSnap = $this->db->getReference('candidates')->getSnapshot();
            if ($candSnap->exists() && $candSnap->getValue()) {
                $posSet = [];
                foreach ($candSnap->getValue() as $c) {
                    if (($c['election_id'] ?? '') === $activeElectionId && ($c['status'] ?? '') === 'approved') {
                        $posSet[$c['position'] ?? 'unknown'] = true;
                        $candidates++;
                    }
                }
                $positions = count($posSet);
            }
        }

        return view('report', compact('turnout', 'byYear', 'positions', 'candidates', 'electionName'));
    }

    public function indexEndOfElection(): View
    {
        $electionsSnap = $this->db->getReference('elections')->getSnapshot();
        $elections     = $electionsSnap->exists() ? ($electionsSnap->getValue() ?? []) : [];

        $targetElection = null;
        $targetId       = null;
        foreach ($elections as $id => $e) {
            if (in_array($e['status'] ?? '', ['active', 'closed'])) {
                $targetElection = $e;
                $targetId       = $id;
            }
        }

        $results     = [];
        $totalVotes  = 0;
        $totalVoters = 0;
        $turnout     = $this->userRepository->voterTurnoutByYearLevel();

        // Total registered voters
        foreach ($turnout as $row) {
            $totalVoters += $row['total_students'];
        }

        if ($targetId) {
            $candSnap  = $this->db->getReference('candidates')->getSnapshot();
            $votesSnap = $this->db->getReference('votes')->getSnapshot();
            $usersSnap = $this->db->getReference('users')->getSnapshot();
            $users     = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];

            // Tally votes for this election
            $tally = [];
            if ($votesSnap->exists() && $votesSnap->getValue()) {
                foreach ($votesSnap->getValue() as $v) {
                    if (($v['election_id'] ?? '') !== $targetId) continue;
                    $pos  = $v['position']     ?? null;
                    $cand = $v['candidate_id'] ?? null;
                    if ($pos && $cand) {
                        $tally[$pos][$cand] = ($tally[$pos][$cand] ?? 0) + 1;
                        $totalVotes++;
                    }
                }
            }

            if ($candSnap->exists() && $candSnap->getValue()) {
                foreach ($candSnap->getValue() as $cid => $c) {
                    if (($c['election_id'] ?? '') !== $targetId || ($c['status'] ?? '') !== 'approved') continue;
                    $pos   = $c['position'] ?? 'Unknown';
                    $uid   = $c['user_id']  ?? null;

                    // Resolve name: use full_name directly or look up user
                    if (!empty($c['full_name'])) {
                        $name = $c['full_name'];
                    } elseif ($uid && isset($users[$uid])) {
                        $u    = $users[$uid];
                        $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Unknown';
                    } else {
                        $name = 'Unknown';
                    }

                    $votes = $tally[$pos][$cid] ?? 0;
                    $results[$pos][] = ['name' => $name, 'votes' => $votes, 'candidate_id' => $cid];
                }

                // Sort each position by votes descending
                foreach ($results as $pos => &$cands) {
                    usort($cands, fn($a, $b) => $b['votes'] <=> $a['votes']);
                }
                unset($cands);
            }
        }

        return view('endofelection', compact('results', 'turnout', 'targetElection', 'totalVotes', 'totalVoters'));
    }

    public function exportEndOfElectionPdf()
    {
        $electionsSnap = $this->db->getReference('elections')->getSnapshot();
        $elections     = $electionsSnap->exists() ? ($electionsSnap->getValue() ?? []) : [];

        $targetElection = null;
        $targetId       = null;
        foreach ($elections as $id => $e) {
            if (in_array($e['status'] ?? '', ['active', 'closed'])) {
                $targetElection = $e;
                $targetId       = $id;
            }
        }

        $results     = [];
        $totalVotes  = 0;
        $totalVoters = 0;
        $turnout     = $this->userRepository->voterTurnoutByYearLevel();

        foreach ($turnout as $row) {
            $totalVoters += $row['total_students'];
        }

        if ($targetId) {
            $candSnap  = $this->db->getReference('candidates')->getSnapshot();
            $votesSnap = $this->db->getReference('votes')->getSnapshot();
            $usersSnap = $this->db->getReference('users')->getSnapshot();
            $users     = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];

            $tally = [];
            if ($votesSnap->exists() && $votesSnap->getValue()) {
                foreach ($votesSnap->getValue() as $v) {
                    if (($v['election_id'] ?? '') !== $targetId) continue;
                    $pos  = $v['position']     ?? null;
                    $cand = $v['candidate_id'] ?? null;
                    if ($pos && $cand) {
                        $tally[$pos][$cand] = ($tally[$pos][$cand] ?? 0) + 1;
                        $totalVotes++;
                    }
                }
            }

            if ($candSnap->exists() && $candSnap->getValue()) {
                foreach ($candSnap->getValue() as $cid => $c) {
                    if (($c['election_id'] ?? '') !== $targetId || ($c['status'] ?? '') !== 'approved') continue;
                    $pos  = $c['position'] ?? 'Unknown';
                    $uid  = $c['user_id']  ?? null;

                    if (!empty($c['full_name'])) {
                        $name = $c['full_name'];
                    } elseif ($uid && isset($users[$uid])) {
                        $u    = $users[$uid];
                        $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Unknown';
                    } else {
                        $name = 'Unknown';
                    }

                    $votes = $tally[$pos][$cid] ?? 0;
                    $results[$pos][] = ['name' => $name, 'votes' => $votes];
                }
                foreach ($results as $pos => &$cands) {
                    usort($cands, fn($a, $b) => $b['votes'] <=> $a['votes']);
                }
                unset($cands);
            }
        }

        return view('endofelection_pdf', compact('results', 'turnout', 'targetElection', 'totalVotes', 'totalVoters'));
    }
}
