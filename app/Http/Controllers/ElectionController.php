<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Contract\Database;
use App\Services\SecurityLogger;
use Illuminate\Support\Facades\Session;

class ElectionController extends Controller
{
    private Database $db;
    private SecurityLogger $logger;

    public function __construct(Database $db, SecurityLogger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function index(): View
    {
        $electionsRef   = $this->db->getReference('elections');
        $activeElection = null;
        $activeElectionId = null;

        $snapshot = $electionsRef->orderByChild('status')->equalTo('active')->getSnapshot();
        if ($snapshot->exists() && $snapshot->getValue() !== null) {
            $elections    = $snapshot->getValue();
            $firstKey     = array_key_first($elections);
            if ($firstKey !== null && $firstKey !== '') {
                $activeElectionId = $firstKey;
                $activeElection   = $elections[$firstKey];
            }
        }

        // If no active, check for upcoming (has schedule but not started yet)
        if (!$activeElection) {
            $allSnap = $electionsRef->getSnapshot();
            if ($allSnap->exists() && $allSnap->getValue()) {
                foreach ($allSnap->getValue() as $eid => $edata) {
                    if (in_array($edata['status'] ?? '', ['upcoming', 'active'])) {
                        $activeElectionId = $eid;
                        $activeElection   = $edata;
                        break;
                    }
                }
            }
        }

        // Compute schedule status for display
        $scheduleStatus = 'not_scheduled';
        $canStart       = false;
        $canClose       = false;

        if ($activeElection && isset($activeElection['date_from'], $activeElection['opening_time'])) {
            $now     = \Carbon\Carbon::now();
            $startDt = \Carbon\Carbon::parse($activeElection['date_from'] . ' ' . $activeElection['opening_time']);
            $endDt   = isset($activeElection['date_to'], $activeElection['closing_time'])
                ? \Carbon\Carbon::parse($activeElection['date_to'] . ' ' . $activeElection['closing_time'])
                : null;

            $currentStatus = $activeElection['status'] ?? 'upcoming';

            if ($currentStatus === 'active') {
                $scheduleStatus = 'active';
                $canClose       = true;
            } elseif ($now->greaterThanOrEqualTo($startDt)) {
                $scheduleStatus = 'ready';   // past start time but not active yet
                $canStart       = true;
            } elseif ($now->lessThan($startDt)) {
                $scheduleStatus = 'upcoming';
                $canStart       = true;      // allow manual early start
            }

            if ($endDt && $now->greaterThan($endDt) && $currentStatus === 'active') {
                $scheduleStatus = 'overdue';
                $canClose       = true;
            }
        } elseif ($activeElection) {
            $canStart = ($activeElection['status'] ?? '') !== 'active';
            $canClose = ($activeElection['status'] ?? '') === 'active';
            $scheduleStatus = $activeElection['status'] ?? 'upcoming';
        }

        $allElections = [];
        $allSnapshot  = $electionsRef->getSnapshot();
        if ($allSnapshot->exists() && $allSnapshot->getValue() !== null) {
            $allElections = $allSnapshot->getValue();
        }

        return view('electioncontrol', [
            'activeElection'   => $activeElection,
            'activeElectionId' => $activeElectionId,
            'allElections'     => $allElections,
            'scheduleStatus'   => $scheduleStatus,
            'canStart'         => $canStart,
            'canClose'         => $canClose,
        ]);
    }

    public function indexPosistionSetup(): View
    {
        $positionsRef      = $this->db->getReference('positions');
        $positionsSnapshot = $positionsRef->getSnapshot();
        $positions         = [];

        if ($positionsSnapshot->exists() && $positionsSnapshot->getValue() !== null) {
            foreach ($positionsSnapshot->getValue() as $posId => $posData) {
                $positions[] = [
                    'id'       => $posId,
                    'name'     => $posData['position_name'] ?? $posData['name'] ?? 'Unknown',
                    'max_vote' => (int) ($posData['max_vote'] ?? $posData['max_votes'] ?? 1),
                ];
            }
            // Sort by standard order
            $order = ['President', 'Vice President', 'Senator', 'Secretary', 'Treasurer', 'Auditor', 'PRO'];
            usort($positions, function ($a, $b) use ($order) {
                $ia = array_search($a['name'], $order);
                $ib = array_search($b['name'], $order);
                return ($ia === false ? 999 : $ia) <=> ($ib === false ? 999 : $ib);
            });
        }

        // Count only candidates for the active election
        $activeElectionId = null;
        $electionSnap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();
        if ($electionSnap->exists() && $electionSnap->getValue()) {
            $firstKey = array_key_first($electionSnap->getValue());
            if ($firstKey !== null && $firstKey !== '') {
                $activeElectionId = $firstKey;
            }
        }

        $totalCandidates = 0;
        $candidatesPerPosition = [];
        $candSnap = $this->db->getReference('candidates')->getSnapshot();
        if ($candSnap->exists() && $candSnap->getValue()) {
            foreach ($candSnap->getValue() as $c) {
                $matchesElection = $activeElectionId
                    ? ($c['election_id'] ?? '') === $activeElectionId
                    : true;
                if ($matchesElection) {
                    $totalCandidates++;
                    $pos = $c['position'] ?? 'Unknown';
                    $candidatesPerPosition[$pos] = ($candidatesPerPosition[$pos] ?? 0) + 1;
                }
            }
        }

        return view('posistionsetup', [
            'positions'             => $positions,
            'totalPositions'        => count($positions),
            'totalCandidates'       => $totalCandidates,
            'candidatesPerPosition' => $candidatesPerPosition,
        ]);
    }

    public function indexCandidateList(): View
    {
        $candidatesRef = $this->db->getReference('candidates');
        $usersRef      = $this->db->getReference('users');
        $electionsRef  = $this->db->getReference('elections');

        // ── Get active election ───────────────────────────────
        $activeElectionId   = null;
        $activeElectionName = null;
        $snap = $electionsRef->orderByChild('status')->equalTo('active')->getSnapshot();
        if ($snap->exists() && $snap->getValue()) {
            $key = array_key_first($snap->getValue());
            if ($key) {
                $activeElectionId   = $key;
                $activeElectionName = $snap->getValue()[$key]['election_name'] ?? null;
            }
        }

        // ── Fetch candidates filtered by active election ──────
        $candidates = [];
        $snapshot   = $candidatesRef->getSnapshot();

        if ($snapshot->exists() && $snapshot->getValue() !== null) {
            foreach ($snapshot->getValue() as $candId => $candData) {
                // Only show candidates for the active election
                if ($activeElectionId && ($candData['election_id'] ?? '') !== $activeElectionId) {
                    continue;
                }

                // Resolve name from users collection
                $candidateName = $candData['full_name'] ?? $candData['name'] ?? 'Unknown';
                $userId        = $candData['user_id'] ?? null;
                if ($userId) {
                    $userSnap = $usersRef->getChild($userId)->getSnapshot();
                    if ($userSnap->exists()) {
                        $u = $userSnap->getValue();
                        $resolved = trim(($u['first_name'] ?? '') . ' ' . ($u['middle_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                        if ($resolved) $candidateName = $resolved;
                    }
                }

                $candidates[] = [
                    'id'       => $candId,
                    'name'     => $candidateName,
                    'position' => $candData['position'] ?? 'Unknown',
                    'course'   => $candData['course']   ?? '',
                    'year'     => $candData['year']     ?? '',
                    'image'    => $candData['image']    ?? '',
                    'status'   => $candData['status']   ?? 'approved',
                ];
            }
        }

        // Sort by position order then name
        $posOrder = ['President','Vice President','Secretary','Treasurer','Auditor','PRO','Senator'];
        usort($candidates, function($a, $b) use ($posOrder) {
            $ia = array_search($a['position'], $posOrder);
            $ib = array_search($b['position'], $posOrder);
            $ia = $ia === false ? 999 : $ia;
            $ib = $ib === false ? 999 : $ib;
            return $ia !== $ib ? $ia <=> $ib : strcmp($a['name'], $b['name']);
        });

        // ── Positions dropdown ────────────────────────────────
        $positionsRef      = $this->db->getReference('positions');
        $positionsSnapshot = $positionsRef->getSnapshot();
        $positions         = [];
        if ($positionsSnapshot->exists() && $positionsSnapshot->getValue() !== null) {
            foreach ($positionsSnapshot->getValue() as $posId => $posData) {
                $positions[] = [
                    'id'   => $posId,
                    'name' => $posData['position_name'] ?? $posData['name'] ?? 'Unknown',
                ];
            }
        }

        // ── Students for autocomplete ─────────────────────────
        $usersSnapshot = $usersRef->getSnapshot();
        $users         = [];
        if ($usersSnapshot->exists() && $usersSnapshot->getValue() !== null) {
            foreach ($usersSnapshot->getValue() as $userId => $userData) {
                if (($userData['role'] ?? 'student') !== 'student') continue;
                $fullName = trim(($userData['first_name'] ?? '') . ' ' . ($userData['middle_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
                if (!$fullName) continue;
                $users[] = [
                    'id'     => $userId,
                    'name'   => $fullName,
                    'course' => $userData['course'] ?? $userData['Course'] ?? '',
                    'year'   => $userData['year'] ?? $userData['year_level'] ?? '',
                ];
            }
        }
        usort($users, fn($a, $b) => strcmp($a['name'], $b['name']));

        return view('candidatelist', [
            'candidates'          => $candidates,
            'positions'           => $positions,
            'users'               => $users,
            'activeElectionId'    => $activeElectionId,
            'activeElectionName'  => $activeElectionName,
        ]);
    }

    public function addCandidate(Request $request)
    {
        try {
            $request->validate([
                'user_id'  => 'required|string',
                'full_name'=> 'required|string',
                'position' => 'required|string',
                'course'   => 'required|string',
                'year'     => 'required|string',
                'image'    => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            ]);

            // ── Election Lock: block if election is active ────────────────
            $electionsRef = $this->db->getReference('elections');
            $activeSnap   = $electionsRef->orderByChild('status')->equalTo('active')->getSnapshot();
            if ($activeSnap->exists() && $activeSnap->getValue()) {
                return redirect()->back()->with('error', 'Candidates cannot be added while an election is active. The candidate list is locked.');
            }

            // ── Candidate window: must be within 1 week before election ───
            $allElecSnap = $electionsRef->getSnapshot();
            $upcomingElection = null;
            if ($allElecSnap->exists() && $allElecSnap->getValue()) {
                foreach ($allElecSnap->getValue() as $eid => $edata) {
                    if (in_array($edata['status'] ?? '', ['upcoming'])) {
                        $upcomingElection = $edata;
                        break;
                    }
                }
            }
            if ($upcomingElection && isset($upcomingElection['date_from'], $upcomingElection['opening_time'])) {
                $startDt    = \Carbon\Carbon::parse($upcomingElection['date_from'] . ' ' . $upcomingElection['opening_time']);
                $windowOpen = $startDt->copy()->subDays(7);
                $now        = \Carbon\Carbon::now();
                if ($now->lessThan($windowOpen)) {
                    $daysUntilWindow = $now->diffInDays($windowOpen);
                    return redirect()->back()->with('error', "Candidate registration opens in {$daysUntilWindow} day(s). You can add candidates starting 1 week before the election.");
                }
            }

            // ── Duplicate candidate check ─────────────────────────────────
            $candidatesRef = $this->db->getReference('candidates');
            $existingSnap  = $candidatesRef->getSnapshot();
            if ($existingSnap->exists() && $existingSnap->getValue()) {
                foreach ($existingSnap->getValue() as $cand) {
                    if (($cand['user_id'] ?? '') === $request->input('user_id')) {
                        $name = $request->input('full_name');
                        return redirect()->back()->with('error', "{$name} is already registered as a candidate in this election.");
                    }
                }
            }

            $candidatesRef = $this->db->getReference('candidates');
            $usersRef = $this->db->getReference('users');
            
            // Get user data to verify student exists
            $userId = $request->input('user_id');
            $userSnapshot = $usersRef->getChild($userId)->getSnapshot();
            
            if (!$userSnapshot->exists()) {
                return redirect()->back()->with('error', 'Selected student not found.');
            }
            
            // Use the full name from the form (already validated from datalist)
            $fullName = $request->input('full_name');
            
            // Handle image upload if provided
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Ensure directory exists
                $uploadPath = public_path('uploads/candidates');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move the file
                $image->move($uploadPath, $imageName);
                $imagePath = 'uploads/candidates/' . $imageName;
                
                \Log::info('Image uploaded successfully', ['path' => $imagePath]);
            }

            // Get active election
            $electionsRef = $this->db->getReference('elections');
            $snapshot = $electionsRef->orderByChild('status')->equalTo('active')->getSnapshot();
            $electionId = null;
            
            if ($snapshot->exists() && $snapshot->getValue() !== null) {
                $elections = $snapshot->getValue();
                $electionId = array_key_first($elections);
            }

            // Create candidate
            $candidateData = [
                'user_id' => $userId,
                'full_name' => $fullName,
                'position' => $request->input('position'),
                'course' => $request->input('course'),
                'year' => $request->input('year'),
                'election_id' => $electionId,
                'status' => 'approved',
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ];
            
            // Only add image path if image was uploaded
            if ($imagePath) {
                $candidateData['image'] = $imagePath;
            }
            
            $newCandidate = $candidatesRef->push($candidateData);

            $user = Session::get('auth_user.role', 'Admin');
            $this->logger->log('info', "Candidate added: {$fullName} for position {$candidateData['position']}", ucfirst($user));

            return redirect()->back()->with('success', 'Candidate added successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error adding candidate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to add candidate: ' . $e->getMessage());
        }
    }

    public function deleteCandidate($id)
    {
        // ── Election Lock ─────────────────────────────────────────────────
        $activeSnap = $this->db->getReference('elections')->orderByChild('status')->equalTo('active')->getSnapshot();
        if ($activeSnap->exists() && $activeSnap->getValue()) {
            return redirect()->back()->with('error', 'Candidates cannot be removed while an election is active.');
        }

        $candidatesRef = $this->db->getReference('candidates');
        // Get name before deleting for the log
        $candData = $candidatesRef->getChild($id)->getSnapshot()->getValue();
        $candName = $candData['full_name'] ?? $id;
        $candPosition = $candData['position'] ?? '';
        $candidatesRef->getChild($id)->remove();

        $user = Session::get('auth_user.role', 'Admin');
        $this->logger->log('info', "Candidate deleted: {$candName} ({$candPosition})", ucfirst($user));

        return redirect()->back()->with('success', 'Candidate deleted successfully.');
    }

    public function updateCandidate(Request $request)
    {
        try {
            $request->validate([
                'candidate_id' => 'required|string',
                'full_name'    => 'required|string|max:255',
                'position'     => 'required|string',
                'course'       => 'required|string',
                'year'         => 'required|string',
                'image'        => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            ]);

            // ── Election Lock ─────────────────────────────────────────────
            $activeSnap = $this->db->getReference('elections')->orderByChild('status')->equalTo('active')->getSnapshot();
            if ($activeSnap->exists() && $activeSnap->getValue()) {
                return redirect()->back()->with('error', 'Candidates cannot be edited while an election is active.');
            }

            $candidateId = $request->input('candidate_id');
            $candidatesRef = $this->db->getReference('candidates');
            
            // Get existing candidate data
            $existingCandidate = $candidatesRef->getChild($candidateId)->getSnapshot()->getValue();
            
            if (!$existingCandidate) {
                return redirect()->back()->with('error', 'Candidate not found.');
            }

            // Prepare update data
            $updateData = [
                'full_name' => $request->input('full_name'),
                'position' => $request->input('position'),
                'course' => $request->input('course'),
                'year' => $request->input('year'),
                'updated_at' => now()->toIso8601String(),
            ];

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Ensure directory exists
                $uploadPath = public_path('uploads/candidates');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Delete old image if exists
                if (isset($existingCandidate['image']) && file_exists(public_path($existingCandidate['image']))) {
                    unlink(public_path($existingCandidate['image']));
                }
                
                // Move the new file
                $image->move($uploadPath, $imageName);
                $updateData['image'] = 'uploads/candidates/' . $imageName;
                
                \Log::info('Image updated successfully', ['path' => $updateData['image']]);
            }

            // Update candidate in Firebase
            $candidatesRef->getChild($candidateId)->update($updateData);

            $user = Session::get('auth_user.role', 'Admin');
            $this->logger->log('info', "Candidate updated: {$updateData['full_name']} ({$updateData['position']})", ucfirst($user));

            return redirect()->back()->with('success', 'Candidate updated successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error updating candidate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update candidate: ' . $e->getMessage());
        }
    }

    public function saveGeneralSettings(Request $request)
    {
        $request->validate([
            'election_name' => 'required|string|max:255',
            'semester' => 'required|string|in:1st Semester,2nd Semester',
            'academic_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
        ], [
            'academic_year.regex' => 'Academic Year must be in format YYYY-YYYY (e.g., 2025-2026)',
            'semester.in' => 'Semester must be either 1st Semester or 2nd Semester',
        ]);

        $electionsRef = $this->db->getReference('elections');
        
        // Check if there's an active election
        $snapshot = $electionsRef->orderByChild('status')->equalTo('active')->getSnapshot();
        $electionId = null;

        if ($snapshot->exists() && $snapshot->getValue() !== null) {
            $elections  = $snapshot->getValue();
            $firstKey   = array_key_first($elections);
            if ($firstKey !== null && $firstKey !== '') {
                $electionId = $firstKey;
            }
        }

        if ($electionId) {
            // Update existing active election — just update settings, keep candidates
            $electionsRef->getChild($electionId)->update([
                'election_name' => $request->input('election_name'),
                'semester'      => $request->input('semester'),
                'academic_year' => $request->input('academic_year'),
                'status'        => 'active',
                'updated_at'    => now()->toIso8601String(),
            ]);

            \Log::info('Updated election settings', [
                'election_id' => $electionId,
                'data' => $request->only(['election_name', 'semester', 'academic_year']),
            ]);
        } else {
            // Creating a brand new election — delete ALL existing candidates first
            $this->deleteAllCandidates();

            $newRef = $electionsRef->push([
                'election_name' => $request->input('election_name'),
                'semester'      => $request->input('semester'),
                'academic_year' => $request->input('academic_year'),
                'status'        => 'active',
                'created_at'    => now()->toIso8601String(),
                'updated_at'    => now()->toIso8601String(),
            ]);

            \Log::info('Created new election — candidates cleared', [
                'election_id' => $newRef->getKey(),
                'data' => $request->only(['election_name', 'semester', 'academic_year']),
            ]);
        }

        // Clear caches
        cache()->forget('active_election_ids');
        cache()->forget('active_election_ids_candidates');

        $user = Session::get('auth_user.role', 'Admin');
        $this->logger->log('info', "Election general settings updated: {$request->input('election_name')} ({$request->input('semester')}, {$request->input('academic_year')})", ucfirst($user));

        return redirect()->back()->with('success', 'General settings saved successfully.');
    }

    public function saveScheduleSettings(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'opening_time' => 'required',
            'closing_time' => 'required',
        ], [
            'date_to.after_or_equal' => 'End date must be after or equal to start date',
        ]);

        $electionsRef = $this->db->getReference('elections');
        $snapshot = $electionsRef->orderByChild('status')->equalTo('active')->getSnapshot();
        $electionId = null;

        if ($snapshot->exists() && $snapshot->getValue() !== null) {
            $elections = $snapshot->getValue();
            $firstKey  = array_key_first($elections);
            if ($firstKey !== null && $firstKey !== '') {
                $electionId = $firstKey;
            }
        }

        if ($electionId) {
            $electionsRef->getChild($electionId)->update([
                'date_from'    => $request->input('date_from'),
                'date_to'      => $request->input('date_to'),
                'opening_time' => $request->input('opening_time'),
                'closing_time' => $request->input('closing_time'),
                'updated_at'   => now()->toIso8601String(),
            ]);
        } else {
            $electionsRef->push([
                'election_name' => 'New Election',
                'semester'      => '1st Semester',
                'academic_year' => date('Y') . '-' . (date('Y') + 1),
                'date_from'     => $request->input('date_from'),
                'date_to'       => $request->input('date_to'),
                'opening_time'  => $request->input('opening_time'),
                'closing_time'  => $request->input('closing_time'),
                'status'        => 'active',
                'created_at'    => now()->toIso8601String(),
                'updated_at'    => now()->toIso8601String(),
            ]);
        }

        cache()->forget('active_election_ids');
        cache()->forget('active_election_ids_candidates');

        $user = Session::get('auth_user.role', 'Admin');
        $this->logger->log('info', "Election schedule updated: {$request->input('date_from')} to {$request->input('date_to')}", ucfirst($user));

        return redirect()->back()->with('success', 'Schedule settings saved successfully.');
    }

    public function updateElectionStatus(Request $request)
    {
        $request->validate([
            'election_id' => 'required|string',
            'status' => 'required|in:active,upcoming,closed',
        ]);

        $electionId = $request->input('election_id');
        $status = $request->input('status');

        if ($status === 'active') {
            $electionsRef = $this->db->getReference('elections');
            $snapshot = $electionsRef->getSnapshot();
            if ($snapshot->exists() && $snapshot->getValue() !== null) {
                foreach ($snapshot->getValue() as $id => $election) {
                    if ($id !== $electionId && ($election['status'] ?? '') === 'active') {
                        $electionsRef->getChild($id)->update(['status' => 'closed', 'updated_at' => now()->toIso8601String()]);
                    }
                }
            }
        }

        $this->db->getReference('elections')->getChild($electionId)->update([
            'status' => $status,
            'updated_at' => now()->toIso8601String(),
        ]);

        cache()->forget('active_election_ids');
        cache()->forget('active_election_ids_candidates');
        cache()->forget('running_candidates');
        cache()->forget('running_candidates_count');
        cache()->forget('live_vote_cast');
        cache()->forget('live_candidate_result');

        $user = Session::get('auth_user.role', 'Admin');
        $this->logger->log('info', "Election status changed to: {$status}", ucfirst($user));

        return redirect()->back()->with('success', 'Election status updated successfully.');
    }

    public function addPosition(Request $request)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'max_vote' => 'required|integer|min:1',
        ]);

        $positionsRef = $this->db->getReference('positions');
        $positionsRef->push([
            'position_name' => $request->input('position_name'),
            'max_vote' => (int)$request->input('max_vote'),
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ]);

        $user = Session::get('auth_user.role', 'Admin');
        $this->logger->log('info', "Position added: {$request->input('position_name')} (max votes: {$request->input('max_vote')})", ucfirst($user));

        return redirect()->back()->with('success', 'Position added successfully.');
    }

    public function updatePosition(Request $request)
    {
        $request->validate([
            'position_id' => 'required|string',
            'position_name' => 'required|string|max:255',
            'max_vote' => 'required|integer|min:1',
        ]);

        $positionId = $request->input('position_id');
        $positionsRef = $this->db->getReference('positions');
        $positionsRef->getChild($positionId)->update([
            'position_name' => $request->input('position_name'),
            'max_vote' => (int)$request->input('max_vote'),
            'updated_at' => now()->toIso8601String(),
        ]);

        $user = Session::get('auth_user.role', 'Admin');
        $this->logger->log('info', "Position updated: {$request->input('position_name')}", ucfirst($user));

        return redirect()->back()->with('success', 'Position updated successfully.');
    }

    public function deletePosition($id)
    {
        $positionsRef = $this->db->getReference('positions');
        $posData = $positionsRef->getChild($id)->getSnapshot()->getValue();
        $posName = $posData['position_name'] ?? $id;
        $positionsRef->getChild($id)->remove();

        $user = Session::get('auth_user.role', 'Admin');
        $this->logger->log('info', "Position deleted: {$posName}", ucfirst($user));

        return redirect()->back()->with('success', 'Position deleted successfully.');
    }

    private function deleteAllCandidates(): void
    {
        $candSnap = $this->db->getReference('candidates')->getSnapshot();
        if (!$candSnap->exists() || !$candSnap->getValue()) return;

        foreach ($candSnap->getValue() as $cid => $c) {
            // Delete photo from disk
            if (!empty($c['image']) && !str_starts_with($c['image'], 'http')) {
                $path = public_path($c['image']);
                if (file_exists($path)) @unlink($path);
            }
            $this->db->getReference('candidates/' . $cid)->remove();
        }

        \Log::info('All candidates deleted for new election cycle.');

        // Reset all student email_verified_at so they must re-verify for the new election
        $this->resetStudentEligibility();
    }

    private function resetStudentEligibility(): void
    {
        $usersSnap = $this->db->getReference('users')->getSnapshot();
        if (!$usersSnap->exists() || !$usersSnap->getValue()) return;

        $count = 0;
        foreach ($usersSnap->getValue() as $uid => $user) {
            if (($user['role'] ?? '') !== 'student') continue;
            if (!empty($user['is_deleted'])) continue;
            if (empty($user['email_verified_at'])) continue;

            $this->db->getReference('users/' . $uid)->update([
                'email_verified_at' => null,
            ]);
            $count++;
        }

        cache()->forget('all_users_raw');
        \Log::info("Reset email_verified_at for {$count} students for new election cycle.");
    }

    public function electionHistory(): \Illuminate\View\View
    {
        $electionsRef = $this->db->getReference('elections');
        $snapshot     = $electionsRef->getSnapshot();
        $elections    = [];

        if ($snapshot->exists() && $snapshot->getValue()) {
            foreach ($snapshot->getValue() as $id => $e) {
                // Count candidates for this election
                $candCount = 0;
                $candSnap  = $this->db->getReference('candidates')->getSnapshot();
                if ($candSnap->exists() && $candSnap->getValue()) {
                    foreach ($candSnap->getValue() as $c) {
                        if (($c['election_id'] ?? '') === $id) $candCount++;
                    }
                }

                // Count votes for this election
                $voteCount  = 0;
                $voterCount = 0;
                $votesSnap  = $this->db->getReference('votes')->getSnapshot();
                $seenVoters = [];
                if ($votesSnap->exists() && $votesSnap->getValue()) {
                    foreach ($votesSnap->getValue() as $v) {
                        if (($v['election_id'] ?? '') === $id) {
                            $voteCount++;
                            $seenVoters[$v['voter_id'] ?? ''] = true;
                        }
                    }
                    $voterCount = count($seenVoters);
                }

                $elections[] = [
                    'id'            => $id,
                    'name'          => $e['election_name']  ?? 'Unnamed Election',
                    'semester'      => $e['semester']       ?? '',
                    'academic_year' => $e['academic_year']  ?? '',
                    'status'        => $e['status']         ?? 'unknown',
                    'date_from'     => $e['date_from']      ?? null,
                    'date_to'       => $e['date_to']        ?? null,
                    'opening_time'  => $e['opening_time']   ?? null,
                    'closing_time'  => $e['closing_time']   ?? null,
                    'created_at'    => $e['created_at']     ?? null,
                    'candidates'    => $candCount,
                    'votes_cast'    => $voteCount,
                    'voters'        => $voterCount,
                ];
            }
        }

        // Sort newest first
        usort($elections, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        return view('election-history', compact('elections'));
    }
}
