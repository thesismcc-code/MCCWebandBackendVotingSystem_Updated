<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Services\ZktecoService;
use App\Models\Fingerprint;
use App\Domain\User\UserRepository;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use App\Helpers\FingerprintHelper;

class FingerPrintController extends Controller
{
    public function __construct(
        private ZktecoService  $zkteco,
        private UserRepository $userRepository,
        private Database       $firebaseDb
    ) {}

    public function index(): View
    {
        $totalStudents = $this->userRepository->countTotalStudents();
        $today = now()->format('Y-m-d');

        // Load all users from Firebase, filter students only
        $allUsers = $this->userRepository->allUsers(99999);

        // Pre-load ALL fingerprints in one query instead of N queries
        $allFingerprints = Fingerprint::all()->keyBy('fid');
        $enrolledToday   = $allFingerprints->filter(
            fn($fp) => $fp->created_at && $fp->created_at->format('Y-m-d') === $today
        )->count();

        $students = collect($allUsers->items())
            ->filter(fn($u) => $u->getRole() === 'student')
            ->map(function ($u) use ($allFingerprints) {
                $fp = $allFingerprints->get($u->getId());

                return [
                    'id'          => $u->getId(),
                    'student_id'  => $u->getStudentId() ?: '—',
                    'name'        => trim($u->getFirstName() . ' ' . $u->getLastName()),
                    'first_name'  => $u->getFirstName(),
                    'middle_name' => $u->getMiddleName() ?? '',
                    'last_name'   => $u->getLastName(),
                    'email'       => $u->getEmail(),
                    'year_level'  => $u->getYearLevel() ?? 'N/A',
                    'course'      => $u->getCourse() ?? 'N/A',
                    'created'     => $u->getCreatedAt()
                                        ? Carbon::parse($u->getCreatedAt())->format('m.d.Y')
                                        : 'N/A',
                    'status'      => $fp ? 'Enrolled' : 'Not Enrolled',
                    'enrolled'    => (bool) $fp,
                    'enrolled_at' => $fp?->created_at?->format('m.d.Y') ?? '—',
                ];
            })
            ->values();

        $enrolledCount = $allFingerprints->count();

        return view('fingerprint', compact('students', 'totalStudents', 'enrolledCount', 'enrolledToday'));
    }

    // ── Device ─────────────────────────────────────────────────

    public function init(): JsonResponse
    {
        try {
            $result = $this->zkteco->init();
            // If device already initialized, treat as success
            $status = $result['status'] ?? 'error';
            if ($status === 'ok' || $status === 'initialized') {
                return response()->json(['status' => 'ok', 'device_count' => $result['device_count'] ?? 1]);
            }
            return response()->json($result);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() === 503 ? 503 : 500;
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], $code);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], 500);
        }
    }

    public function terminate(): JsonResponse
    {
        try {
            return response()->json($this->zkteco->terminate());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], 500);
        }
    }

    public function status(): JsonResponse
    {
        if (!$this->zkteco->isServiceRunning()) {
            return response()->json(['initialized' => false, 'sdk_available' => false, 'service_offline' => true]);
        }
        try {
            return response()->json($this->zkteco->status());
        } catch (\Exception $e) {
            return response()->json(['initialized' => false, 'detail' => $e->getMessage()]);
        }
    }

    // ── Operations ─────────────────────────────────────────────

    public function capture(): JsonResponse
    {
        try {
            return response()->json($this->zkteco->capture());
        } catch (\Exception $e) {
            return response()->json(['captured' => false, 'detail' => $e->getMessage()], 500);
        }
    }

    public function identify(Request $request): JsonResponse
    {
        $request->validate(['template' => 'required|string']);
        try {
            $result = $this->zkteco->identify($request->template);
            if ($result['matched']) {
                $fp   = Fingerprint::where('fid', $result['finger_id'])->first();
                $user = $fp ? $this->userRepository->findById($fp->fid) : null;
                $result['user'] = $user ? [
                    'id'         => $user->getId(),
                    'name'       => trim($user->getFirstName() . ' ' . $user->getLastName()),
                    'student_id' => $user->getStudentId(),
                ] : null;
            }
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['matched' => false, 'detail' => $e->getMessage()], 500);
        }
    }

    public function match(Request $request): JsonResponse
    {
        $request->validate(['template1' => 'required|string', 'template2' => 'required|string']);
        try {
            return response()->json($this->zkteco->match($request->template1, $request->template2));
        } catch (\Exception $e) {
            return response()->json(['matched' => false, 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Register fingerprint for a student.
     * POST: { user_id: "<firebase_key>", templates: [b64, b64, b64] }
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'     => 'required|string',
            'templates'   => 'required|array|size:3',
            'templates.*' => 'required|string',
        ]);

        try {
            $user = $this->userRepository->findById($request->user_id);
            if (!$user) {
                return response()->json(['status' => 'error', 'detail' => 'User not found.'], 404);
            }

            $fingerId = FingerprintHelper::fingerIdFromUserId($user->getId());
            $result   = $this->zkteco->register($fingerId, $request->templates);
            $name     = trim($user->getFirstName() . ' ' . $user->getLastName());
            $now      = Carbon::now()->toDateTimeLocalString();

            // Save to SQLite (local fast lookup)
            Fingerprint::updateOrCreate(
                ['fid' => $user->getId()],
                ['name' => $name, 'template' => $result['template']]
            );

            // Save to Firebase so the mobile app can read it
            $this->firebaseDb->getReference('fingerprints/' . $user->getId())->set([
                'fid'        => $user->getId(),
                'finger_id'  => $fingerId,
                'name'       => $name,
                'student_id' => $user->getStudentId(),
                'template'   => $result['template'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return response()->json(['status' => 'registered', 'finger_id' => $fingerId]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], 500);
        }
    }

    public function loadTemplates(): JsonResponse
    {
        try {
            $members = Fingerprint::all()->map(fn($f) => [
                'finger_id' => FingerprintHelper::fingerIdFromUserId($f->fid),
                'template'  => $f->template,
            ])->toArray();

            // If no templates, just clear the SDK in-memory DB
            if (empty($members)) {
                $this->zkteco->clearDb();
                return response()->json(['loaded' => 0, 'cleared' => true]);
            }

            return response()->json($this->zkteco->loadTemplates($members));
        } catch (\Exception $e) {
            return response()->json(['loaded' => 0, 'detail' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $userId): JsonResponse
    {
        Fingerprint::where('fid', $userId)->delete();
        $this->firebaseDb->getReference('fingerprints/' . $userId)->remove();
        return response()->json(['status' => 'deleted']);
    }

    public function updateStudent(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id'         => 'required|string',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:200',
            'course'     => 'required|string',
            'year_level' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->input('id');

            // Check email uniqueness excluding current user
            $allUsersSnap = $this->firebaseDb->getReference('users')->getSnapshot();
            if ($allUsersSnap->exists() && $allUsersSnap->getValue() !== null) {
                foreach ($allUsersSnap->getValue() as $uid => $u) {
                    if (
                        $uid !== $userId &&
                        isset($u['email']) &&
                        strtolower($u['email']) === strtolower($request->input('email'))
                    ) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Email address is already taken by another account.',
                        ], 422);
                    }
                }
            }

            $update = [
                'first_name'  => $request->input('first_name'),
                'last_name'   => $request->input('last_name'),
                'middle_name' => $request->input('middle_name', ''),
                'email'       => $request->input('email'),
                'course'      => $request->input('course'),
                'year_level'  => $request->input('year_level'),
                'updated_at'  => now()->toIso8601String(),
            ];

            if ($request->filled('password')) {
                $update['password'] = \Illuminate\Support\Facades\Hash::make($request->input('password'));
            }

            $this->firebaseDb->getReference('users/' . $userId)->update($update);

            // Clear user cache
            cache()->forget('all_users_raw');

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function addStudent(Request $request): \Illuminate\Http\JsonResponse
    {
        // Manual validation so errors return JSON, not a redirect
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:200',
            'password'   => 'required|string|min:6',
            'course'     => 'required|string',
            'year_level' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Check email uniqueness — scan all users (no index needed)
            $allUsersSnap = $this->firebaseDb->getReference('users')->getSnapshot();
            if ($allUsersSnap->exists() && $allUsersSnap->getValue() !== null) {
                foreach ($allUsersSnap->getValue() as $uid => $u) {
                    if (
                        isset($u['email']) &&
                        strtolower($u['email']) === strtolower($request->input('email'))
                    ) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Email address is already taken.',
                        ], 422);
                    }
                }
            }

            $userId      = 'STU' . \Illuminate\Support\Str::random(12);
            $currentYear = (int) date('Y');

            // Derive enrollment year from year level
            $yearNum = match($request->input('year_level')) {
                '1st Year' => 0,
                '2nd Year' => 1,
                '3rd Year' => 2,
                '4th Year' => 3,
                default    => 0,
            };
            $enrollYear = $currentYear - $yearNum;
            $shortYear  = substr((string)$enrollYear, -3); // e.g. "026"

            // Count existing students with same enroll year prefix to get next seq
            $prefix  = 'STU-' . $shortYear . '-';
            $allSnap = $this->firebaseDb->getReference('users')
                ->orderByChild('student_id')
                ->startAt($prefix)
                ->endAt($prefix . "\uf8ff")
                ->getSnapshot();
            $seq = ($allSnap->exists() && $allSnap->getValue() !== null)
                ? count($allSnap->getValue()) + 1
                : 1;

            $studentId = $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
            $now       = now()->toIso8601String();

            $userData = [
                'id'                => $userId,
                'first_name'        => $request->input('first_name'),
                'middle_name'       => $request->input('middle_name', ''),
                'last_name'         => $request->input('last_name'),
                'email'             => $request->input('email'),
                'password'          => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                'role'              => 'student',
                'student_id'        => $studentId,
                'course'            => $request->input('course'),
                'year_level'        => $request->input('year_level'),
                'admin_id'          => null,
                'teacher_id'        => null,
                'is_deleted'        => false,
                'email_verified_at' => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            $this->firebaseDb->getReference('users/' . $userId)->set($userData);
            cache()->forget('all_users_raw');

            return response()->json([
                'success' => true,
                'student' => [
                    'id'          => $userId,
                    'student_id'  => $studentId,
                    'name'        => trim($request->input('first_name') . ' ' . $request->input('last_name')),
                    'first_name'  => $request->input('first_name'),
                    'last_name'   => $request->input('last_name'),
                    'middle_name' => $request->input('middle_name', ''),
                    'email'       => $request->input('email'),
                    'course'      => $request->input('course'),
                    'year_level'  => $request->input('year_level'),
                    'created'     => now()->format('m.d.Y'),
                    'status'      => 'Not Enrolled',
                    'enrolled'    => false,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add student: ' . $e->getMessage(),
            ], 500);
        }
    }
}
