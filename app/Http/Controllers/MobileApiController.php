<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Application\RegisterAuth\RegisterAuth;
use App\Application\RegisterCandidates\RegisterCandidates;
use Kreait\Firebase\Contract\Database;
use App\Services\ZktecoService;
use App\Domain\User\UserRepository;
use App\Helpers\FingerprintHelper;

class MobileApiController extends Controller
{
    private RegisterAuth $registerAuth;
    private RegisterCandidates $registerCandidates;
    private Database $db;
    private ZktecoService $zkteco;
    private UserRepository $userRepository;

    public function __construct(
        RegisterAuth $registerAuth,
        RegisterCandidates $registerCandidates,
        Database $db,
        ZktecoService $zkteco,
        UserRepository $userRepository
    ) {
        $this->registerAuth       = $registerAuth;
        $this->registerCandidates = $registerCandidates;
        $this->db = $db;
        $this->zkteco = $zkteco;
        $this->userRepository = $userRepository;
    }

    // GET /mobile/positions
    public function positions(): JsonResponse
    {
        try {
            $snapshot = $this->db->getReference('positions')->getSnapshot();

            if (!$snapshot->exists() || $snapshot->getValue() === null) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $posOrder = ['President','Vice President','Secretary','Treasurer','Auditor','PRO','Senator'];

            $positions = [];
            foreach ($snapshot->getValue() as $posId => $posData) {
                $positions[] = [
                    'id'       => $posId,
                    'name'     => $posData['position_name'] ?? $posData['name'] ?? 'Unknown',
                    'max_vote' => (int) ($posData['max_vote'] ?? $posData['max_votes'] ?? 1),
                ];
            }

            // Sort by standard position order
            usort($positions, function ($a, $b) use ($posOrder) {
                $ia = array_search($a['name'], $posOrder);
                $ib = array_search($b['name'], $posOrder);
                $ia = $ia === false ? 999 : $ia;
                $ib = $ib === false ? 999 : $ib;
                return $ia <=> $ib;
            });

            return response()->json(['success' => true, 'data' => $positions]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch positions.'], 500);
        }
    }

    // POST /mobile/auth/login
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string',
            'password'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $token = $this->registerAuth->loginJwtWithStudentID(
                $request->input('student_id'),
                $request->input('password')
            );

            $user = $this->registerAuth->loginWithStudentID(
                $request->input('student_id'),
                $request->input('password')
            );

            return response()->json([
                'success'      => true,
                'access_token' => $token,
                'token_type'   => 'bearer',
                'user' => [
                    'id'         => $user->getId(),
                    'student_id' => $user->getStudentId(),
                    'first_name' => $user->getFirstName(),
                    'last_name'  => $user->getLastName(),
                    'role'       => $user->getRole(),
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Login failed.'], 500);
        }
    }

    // GET /mobile/auth/me
    public function me(): JsonResponse
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            return response()->json([
                'success' => true,
                'data' => [
                    'id'         => $payload->get('sub'),
                    'student_id' => $payload->get('student_id'),
                    'first_name' => $payload->get('first_name'),
                    'last_name'  => $payload->get('last_name'),
                    'role'       => $payload->get('role'),
                ],
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Token expired.'], 401);
        } catch (TokenInvalidException | JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid token.'], 401);
        }
    }

    // GET /mobile/election/active
    public function activeElection(): JsonResponse
    {
        try {
            $snapshot = $this->db->getReference('elections')
                ->orderByChild('status')->equalTo('active')->getSnapshot();

            if (!$snapshot->exists() || $snapshot->getValue() === null) {
                return response()->json(['success' => false, 'message' => 'No active election.'], 404);
            }

            $elections = $snapshot->getValue();
            $id        = array_key_first($elections);
            $data      = $elections[$id];

            return response()->json([
                'success' => true,
                'data' => [
                    'id'            => $id,
                    'election_name' => $data['election_name'] ?? '',
                    'start_date'    => $data['start_date']    ?? '',
                    'end_date'      => $data['end_date']      ?? '',
                    'status'        => $data['status']        ?? '',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch election.'], 500);
        }
    }

    // GET /mobile/candidates
    public function candidates(): JsonResponse
    {
        try {
            $electionSnap = $this->db->getReference('elections')
                ->orderByChild('status')->equalTo('active')->getSnapshot();

            if (!$electionSnap->exists() || $electionSnap->getValue() === null) {
                return response()->json(['success' => false, 'message' => 'No active election.'], 404);
            }

            $activeElectionId = array_key_first($electionSnap->getValue());
            $candSnap = $this->db->getReference('candidates')->getSnapshot();

            if (!$candSnap->exists() || $candSnap->getValue() === null) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $usersRef  = $this->db->getReference('users');
            $nameCache = [];
            $photoCache = [];

            $resolveName = function (string $userId, array $candidateData) use ($usersRef, &$nameCache): string {
                // First check if candidate has full_name directly
                if (!empty($candidateData['full_name'])) {
                    return $candidateData['full_name'];
                }
                
                // Otherwise try to get from user record
                if (isset($nameCache[$userId])) return $nameCache[$userId];
                $u = $usersRef->getChild($userId)->getValue();
                return $nameCache[$userId] = $u
                    ? trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))
                    : 'Unknown';
            };

            $resolvePhoto = function (string $userId, array $candidateData) use ($usersRef, &$photoCache): string {
                // First check if candidate has image directly
                if (!empty($candidateData['image'])) {
                    // Return full URL for the image
                    $imagePath = $candidateData['image'];
                    // If it's a relative path, make it absolute
                    if (!str_starts_with($imagePath, 'http')) {
                        return url($imagePath);
                    }
                    return $imagePath;
                }
                
                // Otherwise try to get from user record
                if (isset($photoCache[$userId])) return $photoCache[$userId];
                $u = $usersRef->getChild($userId)->getValue();
                $photo = $u['profile_photo'] ?? $u['photo_url'] ?? $u['avatar'] ?? '';
                
                // Make sure photo URL is absolute
                if ($photo && !str_starts_with($photo, 'http')) {
                    $photo = url($photo);
                }
                
                return $photoCache[$userId] = $photo;
            };

            $result = [];
            foreach ($candSnap->getValue() as $candId => $data) {
                if (
                    ($data['election_id'] ?? '') === $activeElectionId &&
                    ($data['status']      ?? '') === 'approved'
                ) {
                    $userId = $data['user_id'] ?? $candId;
                    $result[] = [
                        'id'          => $candId,
                        'name'        => $resolveName($userId, $data),
                        'position'    => $data['position_id'] ?? $data['position'] ?? '',
                        'photo_url'   => $resolvePhoto($userId, $data),
                        'party_list'  => $data['party_list_id'] ?? null,
                        'election_id' => $activeElectionId,
                    ];
                }
            }

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch candidates.'], 500);
        }
    }

    // POST /mobile/vote
    public function castVote(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'votes'                => 'required|array|min:1',
            'votes.*.candidate_id' => 'required|string',
            'votes.*.position'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $payload  = JWTAuth::parseToken()->getPayload();
            $voterId  = (string) $payload->get('sub');

            // ── 1. Check eligibility (email must be verified) ──────────
            $voterData = $this->db->getReference('users/' . $voterId)->getValue();
            if (!$voterData) {
                return response()->json(['success' => false, 'message' => 'Student account not found.'], 404);
            }
            if (empty($voterData['email_verified_at'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not eligible to vote. Please verify your email first.',
                ], 403);
            }

            // ── 2. Check active election ───────────────────────────────
            $electionSnap = $this->db->getReference('elections')
                ->orderByChild('status')->equalTo('active')->getSnapshot();

            if (!$electionSnap->exists() || $electionSnap->getValue() === null) {
                return response()->json(['success' => false, 'message' => 'No active election.'], 404);
            }

            $activeElectionId = array_key_first($electionSnap->getValue());
            $votesRef         = $this->db->getReference('votes');
            $now              = now()->toIso8601String();

            // ── 3. Check already voted (one vote per student per election) ──
            $existingSnap = $votesRef->orderByChild('voter_id')->equalTo($voterId)->getSnapshot();
            if ($existingSnap->exists() && $existingSnap->getValue() !== null) {
                foreach ($existingSnap->getValue() as $v) {
                    if (($v['election_id'] ?? '') === $activeElectionId) {
                        // Log the duplicate attempt
                        $studentId = $voterData['student_id'] ?? $voterId;
                        $name      = trim(($voterData['first_name'] ?? '') . ' ' . ($voterData['last_name'] ?? ''));
                        app(\App\Services\SecurityLogger::class)
                            ->logDuplicateVoteAttempt($studentId, $name, $voterId);

                        return response()->json([
                            'success' => false,
                            'message' => 'You have already voted in this election.',
                        ], 409);
                    }
                }
            }

            // ── 4. Cast votes ──────────────────────────────────────────
            // Enforce max_vote per position from positions collection
            $positionsSnap = $this->db->getReference('positions')->getSnapshot();
            $maxVoteMap    = [];
            if ($positionsSnap->exists() && $positionsSnap->getValue()) {
                foreach ($positionsSnap->getValue() as $p) {
                    $pname = strtolower($p['position_name'] ?? $p['name'] ?? '');
                    $maxVoteMap[$pname] = (int) ($p['max_vote'] ?? 1);
                }
            }

            // Count votes per position in this submission
            $submittedPerPosition = [];
            foreach ($request->input('votes') as $vote) {
                $pos = strtolower($vote['position'] ?? '');
                $submittedPerPosition[$pos] = ($submittedPerPosition[$pos] ?? 0) + 1;
            }

            foreach ($submittedPerPosition as $pos => $count) {
                $max = $maxVoteMap[$pos] ?? 1;
                if ($count > $max) {
                    $label = ucwords($pos);
                    return response()->json([
                        'success' => false,
                        'message' => "You can only vote for {$max} candidate(s) for {$label}.",
                    ], 422);
                }
            }

            foreach ($request->input('votes') as $vote) {
                $votesRef->push([
                    'voter_id'     => $voterId,
                    'candidate_id' => $vote['candidate_id'],
                    'election_id'  => $activeElectionId,
                    'position'     => $vote['position'],
                    'created_at'   => $now,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Vote cast successfully.']);

        } catch (TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Token expired.'], 401);
        } catch (TokenInvalidException | JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to cast vote.'], 500);
        }
    }

    // GET /mobile/voter/status
    public function voterStatus(): JsonResponse
    {
        try {
            $payload  = JWTAuth::parseToken()->getPayload();
            $voterId  = (string) $payload->get('sub');

            // Get voter data for eligibility check
            $voterData  = $this->db->getReference('users/' . $voterId)->getValue();
            $isEligible = !empty($voterData['email_verified_at'] ?? null);

            $electionSnap = $this->db->getReference('elections')
                ->orderByChild('status')->equalTo('active')->getSnapshot();

            if (!$electionSnap->exists() || $electionSnap->getValue() === null) {
                return response()->json([
                    'success'     => true,
                    'has_voted'   => false,
                    'is_eligible' => $isEligible,
                    'election_id' => null,
                ]);
            }

            $activeElectionId = array_key_first($electionSnap->getValue());

            $votesSnap = $this->db->getReference('votes')
                ->orderByChild('voter_id')->equalTo($voterId)->getSnapshot();

            $hasVoted = false;
            if ($votesSnap->exists() && $votesSnap->getValue() !== null) {
                foreach ($votesSnap->getValue() as $v) {
                    if (($v['election_id'] ?? '') === $activeElectionId) {
                        $hasVoted = true;
                        break;
                    }
                }
            }

            return response()->json([
                'success'     => true,
                'has_voted'   => $hasVoted,
                'is_eligible' => $isEligible,
                'election_id' => $activeElectionId,
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Token expired.'], 401);
        } catch (TokenInvalidException | JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to check status.'], 500);
        }
    }
    // â”€â”€ POST /mobile/auth/fingerprint-login â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // Body: { "template": "<base64 fingerprint template>" }
    // Returns JWT + user info if fingerprint matches a registered student
    public function fingerprintLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'template' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            // Ask ZKTeco service to identify the fingerprint
            $result = $this->zkteco->identify($request->input('template'));

            if (empty($result['matched'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fingerprint not recognized.',
                ], 401);
            }

            // Find the user linked to this fingerprint
            $fp   = \App\Models\Fingerprint::where('fid', $result['finger_id'])->first();
            if (!$fp) {
                // finger_id is the crc32 hash â€” reverse lookup via all fingerprints
                $fp = \App\Models\Fingerprint::all()->first(function ($f) use ($result) {
                    return FingerprintHelper::fingerIdFromUserId($f->fid) === (int) $result['finger_id'];
                });
            }

            if (!$fp) {
                return response()->json(['success' => false, 'message' => 'No student linked to this fingerprint.'], 404);
            }

            $user = $this->userRepository->findById($fp->fid);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Student account not found.'], 404);
            }

            // Generate JWT using the proper JWT library
            try {
                $payload = JWTAuth::factory()->customClaims([
                    'sub'        => $user->getId(),
                    'student_id' => $user->getStudentId(),
                    'first_name' => $user->getFirstName(),
                    'last_name'  => $user->getLastName(),
                    'email'      => $user->getEmail(),
                    'role'       => $user->getRole(),
                ])->make();

                $token = JWTAuth::encode($payload)->get();
            } catch (JWTException $e) {
                return response()->json(['success' => false, 'message' => 'Could not create token.'], 500);
            }

            return response()->json([
                'success'      => true,
                'access_token' => $token,
                'token_type'   => 'bearer',
                'user' => [
                    'id'         => $user->getId(),
                    'student_id' => $user->getStudentId(),
                    'first_name' => $user->getFirstName(),
                    'last_name'  => $user->getLastName(),
                    'email'      => $user->getEmail(),
                    'role'       => $user->getRole(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Fingerprint login failed: ' . $e->getMessage()], 500);
        }
    }
}


