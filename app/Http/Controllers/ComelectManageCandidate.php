<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ComelectManageCandidate extends Controller
{
    public function __construct(private Database $db) {}

    public function index(): View
    {
        // Active election
        $electionSnap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();

        $activeElectionId   = null;
        $activeElectionName = null;

        if ($electionSnap->exists() && $electionSnap->getValue()) {
            $activeElectionId   = array_key_first($electionSnap->getValue());
            $activeElectionName = $electionSnap->getValue()[$activeElectionId]['election_name'] ?? 'Active Election';
        }

        // Positions
        $posSnap   = $this->db->getReference('positions')->getSnapshot();
        $positions = [];
        if ($posSnap->exists() && $posSnap->getValue()) {
            foreach ($posSnap->getValue() as $pid => $p) {
                $positions[$pid] = [
                    'id'       => $pid,
                    'name'     => $p['position_name'] ?? $p['name'] ?? 'Unknown',
                    'max_vote' => (int) ($p['max_vote'] ?? $p['max_votes'] ?? 1),
                ];
            }
        }

        // Candidates for active election
        $candSnap   = $this->db->getReference('candidates')->getSnapshot();
        $usersSnap  = $this->db->getReference('users')->getSnapshot();
        $allUsers   = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];

        // Group candidates by position
        $byPosition = [];
        if ($candSnap->exists() && $candSnap->getValue() && $activeElectionId) {
            foreach ($candSnap->getValue() as $cid => $c) {
                if (($c['election_id'] ?? '') !== $activeElectionId) continue;

                $pos  = $c['position_id'] ?? $c['position'] ?? 'Unknown';
                $uid  = $c['user_id'] ?? null;
                $u    = $uid ? ($allUsers[$uid] ?? []) : [];

                $name = !empty($c['full_name'])
                    ? $c['full_name']
                    : (trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Unknown');

                $photo = $c['image'] ?? null;
                if ($photo && !str_starts_with($photo, 'http')) {
                    $photo = asset($photo);
                }

                $byPosition[$pos][] = [
                    'id'         => $cid,
                    'name'       => $name,
                    'course'     => $c['course'] ?? $u['course'] ?? '—',
                    'year_level' => $c['year'] ?? $c['year_level'] ?? $u['year_level'] ?? '—',
                    'party_list' => $c['party_list_id'] ?? $c['party_list'] ?? null,
                    'status'     => $c['status'] ?? 'pending',
                    'photo'      => $photo,
                ];
            }
        }

        // Students list for add candidate dropdown
        $students = [];
        foreach ($allUsers as $uid => $u) {
            if (($u['role'] ?? '') !== 'student') continue;
            if (!empty($u['is_deleted'])) continue;
            $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
            if (!$name) continue;
            $students[] = [
                'id'         => $uid,
                'name'       => $name,
                'course'     => $u['course'] ?? '',
                'year_level' => $u['year_level'] ?? '',
            ];
        }
        usort($students, fn($a, $b) => strcmp($a['name'], $b['name']));

        // Sort positions by standard order
        $order = ['President','Vice President','Secretary','Treasurer','Auditor','PRO'];
        uksort($byPosition, function($a, $b) use ($order) {
            $ia = array_search($a, $order); $ib = array_search($b, $order);
            return ($ia === false ? 999 : $ia) <=> ($ib === false ? 999 : $ib);
        });

        return view('comelecmanagecandidate', compact(
            'byPosition', 'positions', 'students',
            'activeElectionId', 'activeElectionName'
        ));
    }

    public function addCandidate(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id'  => 'required|string',
            'position'    => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $electionSnap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();

        if (!$electionSnap->exists() || !$electionSnap->getValue()) {
            return back()->with('error', 'No active election found.');
        }

        $electionId = array_key_first($electionSnap->getValue());
        $uid        = $request->input('student_id');
        $userData   = $this->db->getReference('users/' . $uid)->getValue();

        if (!$userData) {
            return back()->with('error', 'Student not found.');
        }

        $fullName = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));

        // Check duplicate
        $existing = $this->db->getReference('candidates')
            ->orderByChild('user_id')->equalTo($uid)->getSnapshot();
        if ($existing->exists() && $existing->getValue()) {
            foreach ($existing->getValue() as $c) {
                if (($c['election_id'] ?? '') === $electionId) {
                    return back()->with('error', "{$fullName} is already a candidate in this election.");
                }
            }
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $img      = $request->file('image');
            $imgName  = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
            $dir      = public_path('uploads/candidates');
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $img->move($dir, $imgName);
            $imagePath = 'uploads/candidates/' . $imgName;
        }

        $payload = [
            'user_id'     => $uid,
            'full_name'   => $fullName,
            'position'    => $request->input('position'),
            'position_id' => $request->input('position'),
            'course'      => $userData['course'] ?? '',
            'year_level'  => $userData['year_level'] ?? '',
            'election_id' => $electionId,
            'status'      => 'approved',
            'created_at'  => now()->toIso8601String(),
            'updated_at'  => now()->toIso8601String(),
        ];
        if ($imagePath) $payload['image'] = $imagePath;

        $this->db->getReference('candidates')->push($payload);

        return back()->with('success', "{$fullName} added as candidate successfully.");
    }

    public function deleteCandidate(string $id): RedirectResponse
    {
        $data = $this->db->getReference('candidates/' . $id)->getValue();
        $name = $data['full_name'] ?? $id;
        $this->db->getReference('candidates/' . $id)->remove();
        return back()->with('success', "{$name} removed from candidates.");
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'candidate_id' => 'required|string',
            'status'       => 'required|in:approved,pending,rejected',
        ]);

        $this->db->getReference('candidates/' . $request->input('candidate_id'))->update([
            'status'     => $request->input('status'),
            'updated_at' => now()->toIso8601String(),
        ]);

        return back()->with('success', 'Candidate status updated.');
    }
}
