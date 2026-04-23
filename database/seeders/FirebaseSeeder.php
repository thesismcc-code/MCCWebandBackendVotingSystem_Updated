<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

class FirebaseSeeder extends Seeder
{
    protected Database $db;

    /** Computed in seedVotes() and reused in seedReports() for consistent turnout stats. */
    private int $activeVoterCount  = 0;
    private int $totalStudentCount = 0;

    // ─── LOGGING HELPERS ────────────────────────────────────────────────────────

    private function logSuccess(string $message): void
    {
        $this->command->line("  <fg=green>{$message}</>");
        Log::info($message);
    }

    private function logWarning(string $message): void
    {
        $this->command->line("  <fg=yellow>{$message}</>");
        Log::warning($message);
    }

    private function logError(string $message): void
    {
        $this->command->line("  <fg=red>{$message}</>");
        Log::error($message);
    }

    private function logInfo(string $message): void
    {
        $this->command->line("  {$message}");
        Log::info($message);
    }

    // ─── FIREBASE INIT ──────────────────────────────────────────────────────────

    private function initFirebase(): void
    {
        $credentialsPath = env('FIREBASE_CREDENTIALS');
        $databaseUrl     = env('FIREBASE_DATABASE_URL');

        if (! $credentialsPath) {
            $msg = 'FIREBASE_CREDENTIALS is not set in your .env file. Example: FIREBASE_CREDENTIALS=storage/app/firebase/serviceAccountKey.json';
            $this->logError($msg);
            throw new \RuntimeException($msg);
        }

        if (! $databaseUrl) {
            $msg = 'FIREBASE_DATABASE_URL is not set in your .env file. Example: FIREBASE_DATABASE_URL=https://your-project-id-default-rtdb.firebaseio.com';
            $this->logError($msg);
            throw new \RuntimeException($msg);
        }

        $absolutePath = base_path($credentialsPath);

        if (! file_exists($absolutePath)) {
            $msg = "Firebase credentials file not found at: {$absolutePath}. Make sure you placed serviceAccountKey.json at: {$credentialsPath}";
            $this->logError($msg);
            throw new \RuntimeException($msg);
        }

        $factory = (new Factory)
            ->withServiceAccount($absolutePath)
            ->withDatabaseUri($databaseUrl);

        $this->db = $factory->createDatabase();
    }

    // ─── ENTRY POINT ────────────────────────────────────────────────────────────

    public function run(): void
    {
        $this->initFirebase();

        $this->logInfo('');
        $this->logInfo('Seeding Firebase Realtime Database...');
        $this->logInfo('');

        $this->seedUsers();
        $this->seedElections();
        $this->seedPartyLists();
        $this->seedCandidates();
        $this->seedVotes();
        $this->seedReports();
        $this->seedSystemLogs();

        $this->logInfo('');
        $this->logSuccess('Firebase seeding complete!');
        $this->logInfo('');
    }

    // ─── WRITE HELPER ───────────────────────────────────────────────────────────

    private function set(string $collection, string $key, array $data): void
    {
        try {
            $this->db->getReference("/{$collection}/{$key}")->set($data);
            $this->logSuccess("Written: /{$collection}/{$key}");
        } catch (\Throwable $e) {
            $this->logError("Failed to write /{$collection}/{$key} — {$e->getMessage()}");
        }
    }

    // ─── DATE HELPERS ───────────────────────────────────────────────────────────

    private function now(): string
    {
        return now()->toISOString();
    }

    private function daysAgo(int $days): string
    {
        return now()->subDays($days)->toISOString();
    }

    private function daysFromNow(int $days): string
    {
        return now()->addDays($days)->toISOString();
    }

    private function randomDateBetween(string $start, string $end): string
    {
        $startTs = strtotime($start);
        $endTs   = strtotime($end);
        return date('Y-m-d\TH:i:s', rand($startTs, $endTs));
    }

    /**
     * Generate a student ID encoding the enrollment year and a sequential number.
     * Format: STU-{enrollYear}-{seq}
     * Examples: STU-2023-001, STU-2026-100
     *
     * Enrollment years map to year levels (as of the current year):
     *   currentYear - 0 → 1st Year (enrolled this year)
     *   currentYear - 1 → 2nd Year
     *   currentYear - 2 → 3rd Year
     *   currentYear - 3 → 4th Year
     */
    private function generateStudentId(int $enrollYear, int $seq): string
    {
        return 'STU-' . $enrollYear . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    private function generateUserId(string $role): string
    {
        $prefix = match ($role) {
            'admin'   => 'ADM',
            'student' => 'STU',
            'teacher' => 'THR',
            'sao'     => 'SAO',
            default   => 'USR',
        };
        return $prefix . Str::random(12);
    }

    // ─── COLLECTIONS ────────────────────────────────────────────────────────────

    private function seedUsers(): void
    {
        $this->logInfo('Seeding /users...');

        $password = Hash::make(env('DEFAULT_PASSWORD', 'password123'));

        // ── Fixed staff accounts ──────────────────────────────────────────────
        $staff = [
            'ADMaB3kL9mNpQr' => ['first_name' => 'Alice',  'middle_name' => 'Marie', 'last_name' => 'Santos',    'email' => 'alice.santos@school.edu',    'role' => 'admin',   'student_id' => null, 'teacher_id' => null,             'admin_id' => 'ADMaB3kL9mNpQr'],
            'SAOxK7wP2dYcHj' => ['first_name' => 'Bob',    'middle_name' => 'Cruz',  'last_name' => 'Reyes',     'email' => 'bob.reyes@school.edu',       'role' => 'sao',     'student_id' => null, 'teacher_id' => 'SAOxK7wP2dYcHj', 'admin_id' => null],
            'THRmN4vZ8qEtWs' => ['first_name' => 'Carlos', 'middle_name' => 'Jose',  'last_name' => 'Dela Cruz', 'email' => 'carlos.delacruz@school.edu', 'role' => 'teacher', 'student_id' => null, 'teacher_id' => 'THRmN4vZ8qEtWs', 'admin_id' => null],
        ];

        foreach ($staff as $key => $user) {
            $this->set('users', $key, array_merge($user, [
                'id'                => $key,
                'password'          => $password,
                'email_verified_at' => $this->now(),
                'remember_token'    => null,
                'is_deleted' => false,
                'created_at'        => $this->daysAgo(90),
                'updated_at'        => $this->daysAgo(90),
            ]));
        }

        // ── 10 realistic students (2-3 per year level) ──────────────────────
        $students = [
            // 4th Year (enrolled 2023)
            ['first_name'=>'Juan',     'last_name'=>'Dela Cruz',   'course'=>'Information Technology',  'year'=>2023, 'seq'=>1],
            ['first_name'=>'Maria',    'last_name'=>'Santos',      'course'=>'Computer Science',         'year'=>2023, 'seq'=>2],
            ['first_name'=>'Jose',     'last_name'=>'Reyes',       'course'=>'Business Administration',  'year'=>2023, 'seq'=>3],
            // 3rd Year (enrolled 2024)
            ['first_name'=>'Ana',      'last_name'=>'Garcia',      'course'=>'Nursing',                  'year'=>2024, 'seq'=>1],
            ['first_name'=>'Miguel',   'last_name'=>'Torres',      'course'=>'Engineering',              'year'=>2024, 'seq'=>2],
            ['first_name'=>'Rosa',     'last_name'=>'Lim',         'course'=>'Accountancy',              'year'=>2024, 'seq'=>3],
            // 2nd Year (enrolled 2025)
            ['first_name'=>'Carlos',   'last_name'=>'Aquino',      'course'=>'Criminology',              'year'=>2025, 'seq'=>1],
            ['first_name'=>'Luz',      'last_name'=>'Villanueva',  'course'=>'Education',                'year'=>2025, 'seq'=>2],
            // 1st Year (enrolled 2026)
            ['first_name'=>'Ramon',    'last_name'=>'Castro',      'course'=>'Information Technology',   'year'=>2026, 'seq'=>1],
            ['first_name'=>'Elena',    'last_name'=>'Gonzales',    'course'=>'Computer Science',         'year'=>2026, 'seq'=>2],
        ];

        $currentYear = (int) date('Y');

        foreach ($students as $s) {
            $userId    = $this->generateUserId('student');
            $shortYear = substr((string)$s['year'], -3);
            $studentId = 'STU-' . $shortYear . '-' . str_pad($s['seq'], 3, '0', STR_PAD_LEFT);
            $yearNum   = $currentYear - $s['year'] + 1;
            $yearLevel = match(true) {
                $yearNum === 1 => '1st Year',
                $yearNum === 2 => '2nd Year',
                $yearNum === 3 => '3rd Year',
                $yearNum === 4 => '4th Year',
                default        => "{$yearNum}th Year",
            };
            $daysOld = ($currentYear - $s['year']) * 365 + rand(0, 60);

            $this->set('users', $userId, [
                'id'                => $userId,
                'first_name'        => $s['first_name'],
                'middle_name'       => '',
                'last_name'         => $s['last_name'],
                'email'             => strtolower($s['first_name'] . '.' . $s['last_name']) . '@school.edu',
                'password'          => $password,
                'role'              => 'student',
                'student_id'        => $studentId,
                'course'            => $s['course'],
                'year_level'        => $yearLevel,
                'teacher_id'        => null,
                'admin_id'          => null,
                'is_deleted'        => false,
                'email_verified_at' => $this->daysAgo($daysOld),
                'remember_token'    => null,
                'created_at'        => $this->daysAgo($daysOld),
                'updated_at'        => $this->daysAgo(rand(1, 30)),
            ]);
        }

        $this->logSuccess('10 students seeded successfully.');
    }

    private function seedElections(): void
    {
        $this->logInfo('Seeding /elections...');

        $elections = [
            'election_001' => [
                'id'            => 'election_001',
                'election_name' => 'SSC General Elections 2024',
                'description'   => 'Annual Supreme Student Council election open to all enrolled students.',
                'start_date'    => $this->daysAgo(10),
                'end_date'      => $this->daysAgo(3),
                'status'        => 'closed',
                'created_at'    => $this->daysAgo(30),
                'updated_at'    => $this->daysAgo(3),
            ],
            'election_002' => [
                'id'            => 'election_002',
                'election_name' => 'SSC By-Elections 2025',
                'description'   => 'By-election for vacated SSC positions.',
                'start_date'    => $this->daysFromNow(5),
                'end_date'      => $this->daysFromNow(7),
                'status'        => 'upcoming',
                'created_at'    => $this->now(),
                'updated_at'    => $this->now(),
            ],
            'election_003' => [
                'id'            => 'election_003',
                'election_name' => 'MCC Elections 2026',
                'description'   => 'MCC student council election open to all enrolled students.',
                'start_date'    => $this->daysAgo(1),
                'end_date'      => $this->daysFromNow(1),
                'status'        => 'active',
                'created_at'    => $this->daysAgo(20),
                'updated_at'    => $this->daysAgo(1),
            ],
        ];

        foreach ($elections as $key => $election) {
            $this->set('elections', $key, $election);
        }

        $this->logSuccess('Elections seeded successfully.');
    }

    private function seedPartyLists(): void
    {
        $this->logInfo('Seeding /party_lists...');

        $parties = [
            'party_001' => ['id' => 'party_001', 'party_name' => 'Unity Party',      'description' => 'Committed to unity and inclusive student governance.',  'logo' => 'logos/unity_party.png',       'created_at' => $this->daysAgo(60), 'updated_at' => $this->daysAgo(60)],
            'party_002' => ['id' => 'party_002', 'party_name' => 'Progreso Alliance', 'description' => 'Advocates for academic excellence and student welfare.', 'logo' => 'logos/progreso_alliance.png', 'created_at' => $this->daysAgo(60), 'updated_at' => $this->daysAgo(60)],
            'party_003' => ['id' => 'party_003', 'party_name' => 'Independent',       'description' => 'Non-partisan independent candidates.',                   'logo' => null,                          'created_at' => $this->daysAgo(60), 'updated_at' => $this->daysAgo(60)],
        ];

        foreach ($parties as $key => $party) {
            $this->set('party_lists', $key, $party);
        }

        $this->logSuccess('Party lists seeded successfully.');
    }

    private function seedCandidates(): void
    {
        $this->logInfo('Seeding /candidates...');

        // ── election_001 (closed) — 2 candidates per position using hardcoded student IDs ──
        $election001Candidates = [
            'cand_001' => ['id' => 'cand_001', 'user_id' => 'STUrJ6hD1fXbLn', 'party_list_id' => 'party_001', 'election_id' => 'election_001', 'position' => 'President',      'manifesto' => 'Transparency and student welfare in all SSC decisions.',       'status' => 'approved', 'created_at' => $this->daysAgo(25), 'updated_at' => $this->daysAgo(20)],
            'cand_002' => ['id' => 'cand_002', 'user_id' => 'STUgC5pM3kWoAe', 'party_list_id' => 'party_002', 'election_id' => 'election_001', 'position' => 'President',      'manifesto' => 'Progress through unity — a stronger voice for every student.',  'status' => 'approved', 'created_at' => $this->daysAgo(25), 'updated_at' => $this->daysAgo(20)],
            'cand_003' => ['id' => 'cand_003', 'user_id' => 'STUyT9nB7rVdQz', 'party_list_id' => 'party_001', 'election_id' => 'election_001', 'position' => 'Vice President', 'manifesto' => 'Bridging the gap between students and administration.',         'status' => 'approved', 'created_at' => $this->daysAgo(25), 'updated_at' => $this->daysAgo(20)],
            'cand_004' => ['id' => 'cand_004', 'user_id' => 'STUhR2mK4sJuPf', 'party_list_id' => 'party_003', 'election_id' => 'election_001', 'position' => 'Vice President', 'manifesto' => 'An independent voice dedicated solely to student needs.',       'status' => 'approved', 'created_at' => $this->daysAgo(25), 'updated_at' => $this->daysAgo(20)],
            'cand_005' => ['id' => 'cand_005', 'user_id' => 'STUwL8cF6tNxEv', 'party_list_id' => 'party_002', 'election_id' => 'election_001', 'position' => 'Secretary',      'manifesto' => 'Organized, transparent, and accountable to every student.',    'status' => 'approved', 'created_at' => $this->daysAgo(25), 'updated_at' => $this->daysAgo(20)],
            'cand_006' => ['id' => 'cand_006', 'user_id' => 'ADMaB3kL9mNpQr', 'party_list_id' => 'party_001', 'election_id' => 'election_001', 'position' => 'Treasurer',      'manifesto' => 'Fiscal responsibility and full financial transparency.',         'status' => 'approved', 'created_at' => $this->daysAgo(25), 'updated_at' => $this->daysAgo(20)],
        ];

        foreach ($election001Candidates as $key => $candidate) {
            $this->set('candidates', $key, $candidate);
        }

        // ── election_003 (active) — 3 candidates per position using real students ──
        $this->logInfo('Generating realistic candidates for election_003...');
        
        // Get random students for candidates
        $usersSnapshot = $this->db->getReference('/users')->getSnapshot();
        $students = [];
        
        if ($usersSnapshot->exists() && $usersSnapshot->getValue() !== null) {
            foreach ($usersSnapshot->getValue() as $key => $user) {
                if (isset($user['role']) && $user['role'] === 'student') {
                    $students[] = $key;
                }
            }
        }

        if (count($students) < 18) {
            $this->logWarning('Not enough students to create 18 candidates for election_003. Skipping.');
        } else {
            shuffle($students);
            $students = array_slice($students, 0, 18);

            $positions = ['President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'PRO'];
            $partyLists = ['party_001', 'party_002', 'party_003'];
            $manifestos = [
                'President'      => ['Committed to serving the student body with integrity and dedication.', 'Building a stronger student community through transparent leadership.', 'Your voice, your choice — leadership that listens.'],
                'Vice President' => ['Supporting student welfare and academic excellence.', 'Bridging students to real opportunities and support.', 'An advocate for every student concern.'],
                'Secretary'      => ['Efficient records, clear communication, full accountability.', 'Every decision documented, every voice recorded.', 'Transparent records and open communication.'],
                'Treasurer'      => ['Responsible stewardship of student funds.', 'Full financial transparency and accountability.', 'Independent oversight ensuring funds serve students.'],
                'Auditor'        => ['Rigorous auditing to ensure proper fund management.', 'Checks and balances protecting fund integrity.', 'Independent auditing with zero conflict of interest.'],
                'PRO'            => ['Amplifying the student voice on every platform.', 'Creative, consistent, and student-centered communications.', 'Ensuring all news reaches every student.'],
            ];

            $candIndex = 0;
            $candCounter = 100;

            foreach ($positions as $position) {
                for ($i = 0; $i < 3; $i++) {
                    $studentId = $students[$candIndex++];
                    $candId = 'cand_' . str_pad($candCounter++, 3, '0', STR_PAD_LEFT);
                    
                    $this->set('candidates', $candId, [
                        'id' => $candId,
                        'user_id' => $studentId,
                        'party_list_id' => $partyLists[$i],
                        'election_id' => 'election_003',
                        'position' => $position,
                        'manifesto' => $manifestos[$position][$i],
                        'status' => 'approved',
                        'created_at' => $this->daysAgo(15),
                        'updated_at' => $this->daysAgo(10),
                    ]);
                }
            }

            $this->logSuccess('Created 18 realistic student candidates for election_003.');
        }

        $this->logSuccess('Candidates seeded successfully.');
    }

    private function seedVotes(): void
    {
        $this->logInfo('Seeding /votes...');

        $usersSnapshot = $this->db->getReference('/users')->getSnapshot();
        $allStudentIds = [];

        if ($usersSnapshot->exists() && $usersSnapshot->getValue() !== null) {
            foreach ($usersSnapshot->getValue() as $key => $user) {
                if (isset($user['role']) && $user['role'] === 'student') {
                    $allStudentIds[] = $key;
                }
            }
        }

        if (empty($allStudentIds)) {
            $this->logWarning('No student records found in /users. Skipping vote seeding.');
            return;
        }

        shuffle($allStudentIds);
        $totalStudents           = count($allStudentIds);
        $this->totalStudentCount = $totalStudents;
        $this->logInfo("Found {$totalStudents} student(s) to seed votes for.");

        // ── election_001 (closed) — 100% turnout, 4 positions, 2 candidates each ─
        $election001Config = [
            'election_id' => 'election_001',
            'voter_count' => min(400, $totalStudents),
            'start'       => $this->daysAgo(10),
            'end'         => $this->daysAgo(3),
            'positions'   => [
                'President'      => ['cand_001', 'cand_002'],
                'Vice President' => ['cand_003', 'cand_004'],
                'Secretary'      => ['cand_005'],
                'Treasurer'      => ['cand_006'],
            ],
        ];

        // ── election_003 (active) — 60% turnout, 6 positions, 3 candidates each ─
        $activeVoterCount       = (int) floor($totalStudents * 0.60);
        $this->activeVoterCount = $activeVoterCount;

        $election003Config = [
            'election_id' => 'election_003',
            'voter_count' => $activeVoterCount,
            'start'       => $this->daysAgo(1),
            'end'         => $this->now(),
            'positions'   => [
                'President'      => ['cand_007', 'cand_008', 'cand_009'],
                'Vice President' => ['cand_010', 'cand_011', 'cand_012'],
                'Secretary'      => ['cand_013', 'cand_014', 'cand_015'],
                'Treasurer'      => ['cand_016', 'cand_017', 'cand_018'],
                'Auditor'        => ['cand_019', 'cand_020', 'cand_021'],
                'PRO'            => ['cand_022', 'cand_023', 'cand_024'],
            ],
        ];

        $voteCounter = 1;

        foreach ([$election001Config, $election003Config] as $config) {
            $voters        = array_slice($allStudentIds, 0, $config['voter_count']);
            $positionCount = count($config['positions']);

            shuffle($voters);

            $this->logInfo("Generating votes for {$config['election_id']} ({$config['voter_count']} voters × {$positionCount} positions)...");

            foreach ($voters as $voterId) {
                foreach ($config['positions'] as $position => $candidatePool) {
                    $chosenCandidate = $candidatePool[array_rand($candidatePool)];
                    $voteKey         = 'vote_' . str_pad($voteCounter, 5, '0', STR_PAD_LEFT);

                    $this->set('votes', $voteKey, [
                        'id'           => $voteKey,
                        'voter_id'     => $voterId,
                        'candidate_id' => $chosenCandidate,
                        'election_id'  => $config['election_id'],
                        'position'     => $position,
                        'created_at'   => $this->randomDateBetween($config['start'], $config['end']),
                    ]);

                    $voteCounter++;
                }
            }

            $totalVoteRows = $config['voter_count'] * $positionCount;
            $this->logSuccess("{$config['election_id']}: {$config['voter_count']} voters — {$totalVoteRows} vote rows written.");
        }
    }

    private function seedReports(): void
    {
        $this->logInfo('Seeding /reports...');

        $reports = [
            'report_001' => [
                'id'              => 'report_001',
                'election_id'     => 'election_001',
                'generated_by'    => 'ADMaB3kL9mNpQr',
                'report_type'     => 'vote_summary',
                'status'          => 'completed',
                'file_path'       => 'reports/election_001/vote_summary_2024.pdf',
                'file_name'       => 'vote_summary_2024.pdf',
                'file_format'     => 'pdf',
                'file_size_bytes' => 204800,
                'filters'         => null,
                'summary'         => [
                    'total_votes'     => 400 * 4,
                    'total_voters'    => 400,
                    'turnout_percent' => 100.0,
                    'positions'       => ['President', 'Vice President', 'Secretary', 'Treasurer'],
                ],
                'error_message'   => null,
                'created_at'      => $this->daysAgo(2),
                'updated_at'      => $this->daysAgo(2),
            ],
            'report_002' => [
                'id'              => 'report_002',
                'election_id'     => 'election_001',
                'generated_by'    => 'SAOxK7wP2dYcHj',
                'report_type'     => 'candidate_results',
                'status'          => 'completed',
                'file_path'       => 'reports/election_001/candidate_results_2024.xlsx',
                'file_name'       => 'candidate_results_2024.xlsx',
                'file_format'     => 'xlsx',
                'file_size_bytes' => 51200,
                'filters'         => ['position' => 'President'],
                'summary'         => ['winner' => 'cand_001', 'winner_name' => 'Diana Lim', 'runner_up' => 'cand_002'],
                'error_message'   => null,
                'created_at'      => $this->daysAgo(2),
                'updated_at'      => $this->daysAgo(2),
            ],
            'report_003' => [
                'id'              => 'report_003',
                'election_id'     => 'election_003',
                'generated_by'    => 'ADMaB3kL9mNpQr',
                'report_type'     => 'voter_turnout',
                'status'          => 'pending',
                'file_path'       => null,
                'file_name'       => null,
                'file_format'     => null,
                'file_size_bytes' => null,
                'filters'         => null,
                'summary'         => [
                    'total_votes'     => $this->activeVoterCount * 6,
                    'total_voters'    => $this->activeVoterCount,
                    'turnout_percent' => $this->totalStudentCount > 0
                        ? round(($this->activeVoterCount / $this->totalStudentCount) * 100, 2)
                        : 0.0,
                    'positions'       => ['President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'PRO'],
                ],
                'error_message'   => null,
                'created_at'      => $this->now(),
                'updated_at'      => $this->now(),
            ],
            'report_004' => [
                'id'              => 'report_004',
                'election_id'     => 'election_001',
                'generated_by'    => 'SAOxK7wP2dYcHj',
                'report_type'     => 'party_results',
                'status'          => 'failed',
                'file_path'       => null,
                'file_name'       => null,
                'file_format'     => null,
                'file_size_bytes' => null,
                'filters'         => null,
                'summary'         => null,
                'error_message'   => 'Timeout: PDF generation service unavailable.',
                'created_at'      => $this->daysAgo(1),
                'updated_at'      => $this->daysAgo(1),
            ],
        ];

        foreach ($reports as $key => $report) {
            if ($report['status'] === 'failed') {
                $this->logWarning("Report {$key} has status 'failed' — seeding with error state.");
            }
            $this->set('reports', $key, $report);
        }

        $this->logSuccess('Reports seeded successfully.');
    }

    private function seedSystemLogs(): void
    {
        $this->logInfo('Seeding /system_logs...');

        $logs = [
            'log_001' => ['id' => 'log_001', 'level' => 'info',     'message' => "Election 'SSC General Elections 2024' status changed to closed.",       'context' => ['election_id' => 'election_001', 'changed_by' => 'ADMaB3kL9mNpQr'],                             'created_at' => $this->daysAgo(3)],
            'log_002' => ['id' => 'log_002', 'level' => 'info',     'message' => 'User STUrJ6hD1fXbLn registered as a candidate for position President.',  'context' => ['user_id' => 'STUrJ6hD1fXbLn', 'election_id' => 'election_001'],                               'created_at' => $this->daysAgo(25)],
            'log_003' => ['id' => 'log_003', 'level' => 'warning',  'message' => 'Duplicate vote attempt detected for voter STUyT9nB7rVdQz.',               'context' => ['voter_id' => 'STUyT9nB7rVdQz', 'election_id' => 'election_001', 'position' => 'President'],  'created_at' => $this->daysAgo(8)],
            'log_004' => ['id' => 'log_004', 'level' => 'error',    'message' => 'Report generation failed for report_004.',                                'context' => ['report_id' => 'report_004', 'reason' => 'PDF service timeout'],                               'created_at' => $this->daysAgo(1)],
            'log_005' => ['id' => 'log_005', 'level' => 'critical', 'message' => 'Multiple failed login attempts detected from IP 192.168.1.50.',           'context' => ['ip' => '192.168.1.50', 'attempts' => 10],                                                    'created_at' => $this->now()],
        ];

        foreach ($logs as $key => $log) {
            match ($log['level']) {
                'warning'           => $this->logWarning("System log {$key}: {$log['message']}"),
                'error', 'critical' => $this->logError("System log {$key}: {$log['message']}"),
                default             => $this->logInfo("System log {$key}: {$log['message']}"),
            };
            $this->set('system_logs', $key, $log);
        }

        $this->logSuccess('System logs seeded successfully.');
    }
}
