<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class SAOFinalResult extends Controller
{
    public function __construct(private Database $db) {}

    public function index(): View
    {
        [$results, $electionId, $electionName, $isPublished] = $this->buildResults();
        return view('saofinalresult', compact('results', 'electionId', 'electionName', 'isPublished'));
    }

    public function publish(Request $request): RedirectResponse
    {
        // ── 1. Find active election ───────────────────────────────────
        $snap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();

        if (!$snap->exists() || !$snap->getValue()) {
            return back()->with('error', 'No active election to publish.');
        }

        $electionId   = array_key_first($snap->getValue());
        $electionData = $snap->getValue()[$electionId];

        // ── 2. Build results BEFORE deleting candidates ───────────────
        [$results] = $this->buildResultsForElection($electionId);

        // ── 3. Mark election as closed + results_published ────────────
        $now = Carbon::now()->toDateTimeLocalString();
        $this->db->getReference('elections/' . $electionId)->update([
            'status'            => 'closed',
            'results_published' => true,
            'published_at'      => $now,
        ]);

        // ── 4. Delete all candidates for this election ────────────────
        $this->deleteCandidatesForElection($electionId);

        // ── 5. Reset all student email_verified_at so they re-verify for next election
        $this->resetStudentEligibility();

        // ── 6. Email all students ─────────────────────────────────────
        $usersSnap = $this->db->getReference('users')->getSnapshot();
        if ($usersSnap->exists() && $usersSnap->getValue()) {
            foreach ($usersSnap->getValue() as $user) {
                if (($user['role'] ?? '') !== 'student') continue;
                if (empty($user['email'])) continue;
                if (!empty($user['is_deleted'])) continue;

                try {
                    $email        = $user['email'];
                    $firstName    = $user['first_name'] ?? 'Student';
                    $electionName = $electionData['election_name'] ?? 'MCC Election';

                    Mail::send([], [], function ($message) use ($email, $firstName, $electionName, $results) {
                        $message->to($email)
                            ->subject("📢 Official Election Results — {$electionName}")
                            ->html($this->buildResultsEmail($firstName, $electionName, $results));
                    });
                } catch (\Exception $e) {
                    Log::warning('Failed to send results email', ['email' => $user['email'] ?? '', 'error' => $e->getMessage()]);
                }
            }
        }

        // ── 6. Clear all caches ───────────────────────────────────────
        $this->clearElectionCaches();

        return back()->with('success', 'Official results published, students notified, and candidates cleared for the next election.');
    }

    private function deleteCandidatesForElection(string $electionId): void
    {
        $candSnap = $this->db->getReference('candidates')
            ->orderByChild('election_id')->equalTo($electionId)->getSnapshot();

        if ($candSnap->exists() && $candSnap->getValue()) {
            foreach ($candSnap->getValue() as $cid => $c) {
                // Delete candidate photo from disk if exists
                if (!empty($c['image']) && !str_starts_with($c['image'], 'http')) {
                    $path = public_path($c['image']);
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
                $this->db->getReference('candidates/' . $cid)->remove();
            }
        }

        Log::info("Deleted all candidates for election: {$electionId}");
    }

    private function resetStudentEligibility(): void
    {
        $usersSnap = $this->db->getReference('users')->getSnapshot();
        if (!$usersSnap->exists() || !$usersSnap->getValue()) return;

        $count = 0;
        foreach ($usersSnap->getValue() as $uid => $user) {
            if (($user['role'] ?? '') !== 'student') continue;
            if (!empty($user['is_deleted'])) continue;
            if (empty($user['email_verified_at'])) continue; // already not verified

            $this->db->getReference('users/' . $uid)->update([
                'email_verified_at' => null,
            ]);
            $count++;
        }

        // Bust the users cache so eligibility page reflects immediately
        cache()->forget('all_users_raw');

        Log::info("Reset email_verified_at for {$count} students for new election cycle.");
    }

    private function clearElectionCaches(): void
    {
        foreach ([
            'active_election_ids', 'active_election_ids_candidates',
            'running_candidates', 'running_candidates_count',
            'live_vote_cast', 'live_candidate_result', 'all_users_raw',
        ] as $key) {
            cache()->forget($key);
        }
    }

    // ── Shared result builder ─────────────────────────────────────────
    private function buildResults(): array
    {
        // Try active first, then closed+published
        $snap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();

        $electionId   = null;
        $electionName = 'No Active Election';
        $isPublished  = false;

        if ($snap->exists() && $snap->getValue()) {
            $electionId   = array_key_first($snap->getValue());
            $electionData = $snap->getValue()[$electionId];
            $electionName = $electionData['election_name'] ?? 'Active Election';
            $isPublished  = !empty($electionData['results_published']);
        } else {
            // Look for most recent closed+published election
            $allSnap = $this->db->getReference('elections')->getSnapshot();
            if ($allSnap->exists() && $allSnap->getValue()) {
                foreach ($allSnap->getValue() as $eid => $edata) {
                    if (($edata['status'] ?? '') === 'closed' && !empty($edata['results_published'])) {
                        $electionId   = $eid;
                        $electionName = $edata['election_name'] ?? 'Election';
                        $isPublished  = true;
                        break;
                    }
                }
            }
        }

        if (!$electionId) {
            return [[], null, $electionName, false];
        }

        [$results] = $this->buildResultsForElection($electionId);
        return [$results, $electionId, $electionName, $isPublished];
    }

    public function buildResultsForElection(string $electionId): array
    {
        $candSnap  = $this->db->getReference('candidates')->getSnapshot();
        $votesSnap = $this->db->getReference('votes')
            ->orderByChild('election_id')->equalTo($electionId)->getSnapshot();
        $usersSnap = $this->db->getReference('users')->getSnapshot();
        $users     = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];

        // Tally votes
        $tally = [];
        if ($votesSnap->exists() && $votesSnap->getValue()) {
            foreach ($votesSnap->getValue() as $v) {
                $pos  = $v['position']     ?? null;
                $cand = $v['candidate_id'] ?? null;
                if ($pos && $cand) $tally[$pos][$cand] = ($tally[$pos][$cand] ?? 0) + 1;
            }
        }

        $positionOrder = ['President','Vice President','Secretary','Treasurer','Auditor','PRO'];
        $results = [];

        if ($candSnap->exists() && $candSnap->getValue()) {
            foreach ($candSnap->getValue() as $cid => $c) {
                if (($c['election_id'] ?? '') !== $electionId) continue;
                if (($c['status'] ?? '') !== 'approved') continue;

                $pos  = $c['position_id'] ?? $c['position'] ?? 'Unknown';
                $uid  = $c['user_id'] ?? $cid;
                $u    = $users[$uid] ?? [];

                // Resolve name — check candidate full_name first, then user record
                if (!empty($c['full_name'])) {
                    $name = $c['full_name'];
                } else {
                    $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Unknown';
                }

                $votes = $tally[$pos][$cid] ?? 0;
                $results[$pos][] = ['name' => $name, 'votes' => $votes, 'candidate_id' => $cid];
            }

            foreach ($results as $pos => &$cands) {
                usort($cands, fn($a, $b) => $b['votes'] <=> $a['votes']);
            }
            unset($cands);

            // Sort by position order
            uksort($results, function ($a, $b) use ($positionOrder) {
                $ia = array_search($a, $positionOrder);
                $ib = array_search($b, $positionOrder);
                $ia = $ia === false ? 999 : $ia;
                $ib = $ib === false ? 999 : $ib;
                return $ia <=> $ib;
            });
        }

        return [$results];
    }

    private function buildResultsEmail(string $firstName, string $electionName, array $results): string
    {
        $rows = '';
        foreach ($results as $position => $candidates) {
            $winner = $candidates[0] ?? null;
            $rows .= "<tr><td colspan='2' style='background:#e8f0fe;padding:10px 16px;font-weight:700;font-size:14px;color:#152B66;text-transform:uppercase;'>{$position}</td></tr>";
            foreach ($candidates as $c) {
                $bold = ($winner && $c['candidate_id'] === $winner['candidate_id']) ? 'font-weight:700;' : '';
                $rows .= "<tr><td style='padding:8px 16px;{$bold}color:#1f2937;'>{$c['name']}</td><td style='padding:8px 16px;text-align:right;{$bold}color:#1f2937;'>{$c['votes']} votes</td></tr>";
            }
            if ($winner) {
                $rows .= "<tr><td colspan='2' style='background:#dcfce7;padding:10px 16px;font-weight:800;color:#166534;'>🏆 WINNER: {$winner['name']} — {$winner['votes']} votes</td></tr>";
            }
            $rows .= "<tr><td colspan='2' style='height:12px;'></td></tr>";
        }

        return <<<HTML
        <div style="font-family:Inter,Arial,sans-serif;max-width:600px;margin:0 auto;padding:32px;background:#f9fafb;border-radius:12px;">
            <div style="text-align:center;margin-bottom:24px;">
                <h1 style="color:#152B66;font-size:22px;font-weight:800;margin:0;">MCC Digital Voting System</h1>
                <p style="color:#6b7280;font-size:14px;margin-top:6px;">Official Election Results</p>
            </div>
            <div style="background:#fff;border-radius:10px;padding:24px;border:1px solid #e5e7eb;">
                <p style="color:#374151;font-size:15px;margin-bottom:16px;">Hi <strong>{$firstName}</strong>,</p>
                <p style="color:#374151;font-size:15px;margin-bottom:20px;">The official results for <strong>{$electionName}</strong> have been published. Here are the winners:</p>
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    {$rows}
                </table>
                <p style="color:#6b7280;font-size:13px;margin-top:20px;">You can also view the full results on your student dashboard.</p>
            </div>
            <p style="color:#9ca3af;font-size:12px;text-align:center;margin-top:20px;">Mandaue City College — Digital Voting System</p>
        </div>
        HTML;
    }
}
