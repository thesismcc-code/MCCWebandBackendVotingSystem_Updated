<?php

namespace App\Eloquent\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Database\Reference;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class EloquentUserRepository implements UserRepository
{
    private Reference $db;
    private Reference $votesDb;
    private Reference $electionsDb;
    private const COLLECTIONS            = 'users';
    private const VOTES_COLLECTIONS      = 'votes';
    private const ELECTIONS_COLLECTIONS  = 'elections';

    public function __construct(Database $db)
    {
        $this->db          = $db->getReference(self::COLLECTIONS);
        $this->votesDb     = $db->getReference(self::VOTES_COLLECTIONS);
        $this->electionsDb = $db->getReference(self::ELECTIONS_COLLECTIONS);
    }

    public function findById(string $id): ?User
    {
        $data = $this->db->getChild($id)->getValue();
        if (!$data) return null;

        return $this->toUserWithPassword($id, $data);
    }

    public function findByEmail(string $email): ?User
    {
        $collection = $this->db->getSnapshot();
        if (!$collection->exists() || $collection->getValue() === null) {
            return null;
        }

        $snapshot = $this->db
            ->orderByChild('email')
            ->equalTo($email)
            ->getSnapshot();

        if (!$snapshot->exists() || $snapshot->getValue() === null) {
            return null;
        }

        foreach ($snapshot->getValue() as $key => $data) {
            return $this->toUserWithPassword((string) $key, $data);
        }

        return null;
    }

    public function findByStudentID(string $studentId): ?User
    {
        $snapshot = $this->db
            ->orderByChild('student_id')
            ->equalTo($studentId)
            ->getSnapshot();

        if (!$snapshot->exists() || $snapshot->getValue() === null) {
            return null;
        }

        foreach ($snapshot->getValue() as $key => $data) {
            if (isset($data['role']) && $data['role'] === 'student') {
                return $this->toUserWithPassword((string) $key, $data);
            }
        }

        return null;
    }

    public function validateStudentID(string $studentId): bool
    {
        $snapshot = $this->db
            ->orderByChild('student_id')
            ->equalTo($studentId)
            ->getSnapshot();

        if (!$snapshot->exists() || $snapshot->getValue() === null) {
            return false;
        }

        foreach ($snapshot->getValue() as $data) {
            if (isset($data['role']) && $data['role'] === 'student') {
                return true;
            }
        }

        return false;
    }

    public function saveNewUser(User $data): ?User
    {
        $now = Carbon::now()->toDateTimeLocalString();

        $payload = [
            ...$data->toArray(),
            'password'   => Hash::make($data->getPassword()),
            'is_deleted' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($data->getId()) {
            $this->db->getChild($data->getId())->set($payload);
            $id = $data->getId();
        } else {
            $newRef = $this->db->push($payload);
            $id     = $newRef->getKey();
        }

        Log::info('New account created', [
            'firebase_key' => $id,
            'email'        => $data->getEmail(),
            'role'         => $data->getRole(),
            'first_name'   => $data->getFirstName(),
            'last_name'    => $data->getLastName(),
            'admin_id'     => $data->getAdminId(),
            'student_id'   => $data->getStudentId(),
            'comelec_id'   => $data->getComelecId(),
            'created_at'   => $now,
        ]);

        cache()->forget('all_users_raw');

        return $this->toUser((string) $id, [...$payload, 'id' => $id]);
    }

    public function updateUser(User $data): ?User
    {
        $now = Carbon::now()->toDateTimeLocalString();

        $payload = [
            ...$data->toArray(),
            'updated_at' => $now,
        ];

        $this->db->getChild($data->getId())->update($payload);
        cache()->forget('all_users_raw');
        return $this->toUser($data->getId(), $payload);
    }

    public function deleteUser(string $id): void
    {
        $this->db->getChild($id)->update([
            'is_deleted' => true,
            'updated_at' => Carbon::now()->toDateTimeLocalString(),
        ]);

        cache()->forget('all_users_raw');
    }

    public function allUsers(int $perPage, ?string $schoolYearFilter = null): LengthAwarePaginator
    {
        $users = $this->getUsersCollection()
            ->map(fn($data, $key) => $this->toUser((string) $key, $data))
            ->when($schoolYearFilter, function ($collection) use ($schoolYearFilter) {
                [$startYear, $endYear] = explode('-', $schoolYearFilter);
                $start = Carbon::create($startYear, 8, 1)->startOfDay();
                $end = Carbon::create($endYear, 7, 31)->endOfDay();

                return $collection->filter(
                    fn($user) => Carbon::parse($user->getCreatedAt())->between($start, $end)
                );
            })
            ->values();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $users->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $users->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function countStudentsByYearPrefix(string $prefix): int
    {
        $snapshot = $this->db
            ->orderByChild('student_id')
            ->startAt($prefix)
            ->endAt($prefix . '\uf8ff')
            ->getSnapshot();

        if (!$snapshot->exists() || $snapshot->getValue() === null) {
            return 0;
        }

        $count = 0;

        foreach ($snapshot->getValue() as $data) {
            if (
                isset($data['role'], $data['student_id']) &&
                $data['role'] === 'student' &&
                str_starts_with($data['student_id'], $prefix)
            ) {
                $count++;
            }
        }

        return $count;
    }

    private function getActiveElectionIds(): array
    {
        return cache()->remember('active_election_ids', now()->addMinutes(5), function () {
            $snapshot = $this->electionsDb
                ->orderByChild('status')
                ->equalTo('active')
                ->getSnapshot();

            if (!$snapshot->exists() || $snapshot->getValue() === null) {
                return [];
            }

            return array_keys($snapshot->getValue());
        });
    }

    public function countStudentVoters(?array $activeElectionIds = null): int
    {
        $votesSnapshot = $this->votesDb->getSnapshot();

        if (!$votesSnapshot->exists() || $votesSnapshot->getValue() === null) {
            return 0;
        }

        $uniqueVoterIds = [];
        foreach ($votesSnapshot->getValue() as $vote) {
            if (!isset($vote['voter_id'])) {
                continue;
            }

            if (
                $activeElectionIds !== null &&
                (!isset($vote['election_id']) || !in_array($vote['election_id'], $activeElectionIds, true))
            ) {
                continue;
            }

            $uniqueVoterIds[$vote['voter_id']] = true;
        }

        if (empty($uniqueVoterIds)) {
            return 0;
        }

        // Use cached collection instead of direct getValue()
        $allUsers = $this->getUsersCollection();

        if ($allUsers->isEmpty()) {
            return 0;
        }

        return $allUsers->filter(function ($user, $userId) use ($uniqueVoterIds) {
            return isset($uniqueVoterIds[$userId]) && 
                   isset($user['role']) && 
                   $user['role'] === 'student';
        })->count();
    }

    public function countTotalStudents(): int
    {
        return $this->getUsersCollection()
            ->filter(fn($user) => isset($user['role']) && $user['role'] === 'student')
            ->count();
    }

    public function getVoterTurnout(): array
    {
        $activeElectionIds = $this->getActiveElectionIds();

        if (empty($activeElectionIds)) {
            return [
                'voters'          => 0,
                'total_students'  => $this->countTotalStudents(),
                'turnout_percent' => 0.0,
            ];
        }

        $voters        = $this->countStudentVoters($activeElectionIds);
        $totalStudents = $this->countTotalStudents();

        return [
            'voters'          => $voters,
            'total_students'  => $totalStudents,
            'turnout_percent' => $totalStudents > 0
                ? round(($voters / $totalStudents) * 100, 2)
                : 0.0,
        ];
    }

    public function realtimeVoterTurnout(): array
    {
        $activeElectionIds = $this->getActiveElectionIds();
        $totalStudents     = $this->countTotalStudents();

        if (empty($activeElectionIds) || $totalStudents === 0) {
            return [
                'total_students'  => $totalStudents,
                'voted_count'     => 0,
                'not_yet_voted'   => $totalStudents,
                'turnout_percent' => 0.0,
            ];
        }

        $votedCount = $this->countStudentVoters($activeElectionIds);

        return [
            'total_students'  => $totalStudents,
            'voted_count'     => $votedCount,
            'not_yet_voted'   => $totalStudents - $votedCount,
            'turnout_percent' => round(($votedCount / $totalStudents) * 100, 2),
        ];
    }

    public function voterTurnoutByYearLevel(): array
    {
        $activeElectionIds = $this->getActiveElectionIds();

        $votedByStudent = [];

        if (!empty($activeElectionIds)) {
            $votesSnapshot = $this->votesDb->getSnapshot();

            if ($votesSnapshot->exists() && $votesSnapshot->getValue() !== null) {
                foreach ($votesSnapshot->getValue() as $vote) {
                    if (
                        isset($vote['voter_id'], $vote['election_id']) &&
                        in_array($vote['election_id'], $activeElectionIds, true)
                    ) {
                        $votedByStudent[$vote['voter_id']] = true;
                    }
                }
            }
        }

        $allUsers = $this->getUsersCollection();

        if ($allUsers->isEmpty()) {
            return [];
        }

        $yearGroups = [];

        foreach ($allUsers as $userId => $user) {
            if (!isset($user['role'], $user['student_id']) || $user['role'] !== 'student') {
                continue;
            }

            // Use the year field directly from user data
            $rawYear = $user['year'] ?? $user['Year'] ?? $user['year_level'] ?? null;

            if ($rawYear === null || $rawYear === '') {
                continue;
            }

            // Normalize: extract numeric value (handles "1st Year", "1", "2nd Year", etc.)
            $yearNum = (int) preg_replace('/[^0-9]/', '', (string) $rawYear);

            if ($yearNum < 1 || $yearNum > 6) {
                continue;
            }

            if (!isset($yearGroups[$yearNum])) {
                $yearGroups[$yearNum] = ['total' => 0, 'voted' => 0];
            }

            $yearGroups[$yearNum]['total']++;

            if (isset($votedByStudent[$userId])) {
                $yearGroups[$yearNum]['voted']++;
            }
        }

        // Sort descending: 4th Year first, 1st Year last
        krsort($yearGroups);

        $result = [];

        foreach ($yearGroups as $yearNum => $counts) {
            $total = $counts['total'];
            $voted = $counts['voted'];

            $label = match ($yearNum) {
                1 => '1st Year',
                2 => '2nd Year',
                3 => '3rd Year',
                4 => '4th Year',
                5 => '5th Year',
                default => "{$yearNum}th Year",
            };

            $result[] = [
                'year_level'      => $label,
                'enroll_year'     => $yearNum,
                'total_students'  => $total,
                'voted'           => $voted,
                'not_yet_voted'   => $total - $voted,
                'turnout_percent' => $total > 0 ? round(($voted / $total) * 100, 2) : 0.0,
            ];
        }

        return $result;
    }

    private function toUser(mixed $id, array $data): User
    {
        return new User(
            id: (string) $id,
            first_name: $data['first_name'] ?? '',
            middle_name: $data['middle_name'] ?? '',
            last_name: $data['last_name'] ?? '',
            email: $data['email'] ?? '',
            password: '',
            role: $data['role'] ?? '',
            admin_id: $data['admin_id'] ?? null,
            student_id: $data['student_id'] ?? null,
            comelec_id: $data['comelec_id'] ?? null,
            email_verified_at: $data['email_verified_at'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            year_level: $data['year_level'] ?? null,
            course: $data['course'] ?? null,
        );
    }

    private function toUserWithPassword(mixed $id, array $data): User
    {
        return new User(
            id: (string) $id,
            first_name: $data['first_name'] ?? '',
            middle_name: $data['middle_name'] ?? '',
            last_name: $data['last_name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            role: $data['role'] ?? '',
            admin_id: $data['admin_id'] ?? null,
            student_id: $data['student_id'] ?? null,
            comelec_id: $data['comelec_id'] ?? null,
            email_verified_at: $data['email_verified_at'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            year_level: $data['year_level'] ?? null,
            course: $data['course'] ?? null,
        );
    }
    public function countUsersSummary(): array
    {
        $users = $this->getUsersCollection();

        if ($users->isEmpty()) {
            return ['total' => 0, 'comelec' => 0, 'sao' => 0, 'admin' => 0];
        }

        return [
            'total'   => $users->count(),
            'comelec' => $users->filter(fn($u) => ($u['role'] ?? '') === 'comelec')->count(),
            'sao'     => $users->filter(fn($u) => ($u['role'] ?? '') === 'sao')->count(),
            'admin'   => $users->filter(fn($u) => ($u['role'] ?? '') === 'admin')->count(),
        ];
    }

    public function getStudentsEligibility(int $perPage, ?string $search = null, ?string $course = null, ?string $status = null): array
    {
        // Always fetch fresh data — bypass cache
        $snapshot = $this->db->getSnapshot();
        $allUsers = collect();
        if ($snapshot->exists() && $snapshot->getValue() !== null) {
            $allUsers = collect($snapshot->getValue())->filter(fn($user) => empty($user['is_deleted']));
        }

        $students = $allUsers->filter(fn($user) => isset($user['role']) && $user['role'] === 'student');

        // Search filter
        if ($search) {
            $lowerSearch = strtolower($search);
            $students = $students->filter(function ($user) use ($lowerSearch) {
                $fullName  = strtolower(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                $studentId = strtolower($user['student_id'] ?? '');
                return str_contains($fullName, $lowerSearch) || str_contains($studentId, $lowerSearch);
            });
        }

        // Course filter
        if ($course && $course !== 'all') {
            $students = $students->filter(fn($user) => strtolower($user['course'] ?? '') === strtolower($course));
        }

        // Compute totals before status filter
        $total       = $students->count();
        $eligible    = $students->filter(fn($u) => !empty($u['email_verified_at']))->count();
        $notEligible = $total - $eligible;

        // Status filter
        if ($status === 'eligible') {
            $students = $students->filter(fn($u) => !empty($u['email_verified_at']));
        } elseif ($status === 'not_eligible') {
            $students = $students->filter(fn($u) => empty($u['email_verified_at']));
        }

        // Paginate
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $sliced      = $students->slice(($currentPage - 1) * $perPage, $perPage);
        $items       = $sliced->map(fn($data, $key) => $this->toUser((string) $key, $data))->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $students->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'total'       => $total,
            'eligible'    => $eligible,
            'notEligible' => $notEligible,
            'paginator'   => $paginator,
        ];
    }

    private function getUsersCollection(): Collection
    {
        return cache()->remember('all_users_raw', now()->addMinutes(10), function () {
            $snapshot = $this->db->getSnapshot();
            if (!$snapshot->exists() || $snapshot->getValue() === null) {
                return collect();
            }
            return collect($snapshot->getValue())->filter(fn($user) => empty($user['is_deleted']));
        });
    }
}
