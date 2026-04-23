<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class ResetStudentEligibility extends Command
{
    protected $signature   = 'students:reset-eligibility';
    protected $description = 'Reset email_verified_at for all students (new election cycle)';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        $usersSnap = $this->db->getReference('users')->getSnapshot();

        if (!$usersSnap->exists() || !$usersSnap->getValue()) {
            $this->error('No users found.');
            return;
        }

        $count = 0;
        foreach ($usersSnap->getValue() as $uid => $user) {
            if (($user['role'] ?? '') !== 'student') continue;
            if (!empty($user['is_deleted'])) continue;

            $this->db->getReference('users/' . $uid)->update([
                'email_verified_at' => null,
            ]);
            $count++;
            $this->line("  Reset: " . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') . " ({$uid})");
        }

        cache()->forget('all_users_raw');

        $this->info("✓ Reset email_verified_at for {$count} students.");
    }
}
