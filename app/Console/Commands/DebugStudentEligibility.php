<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class DebugStudentEligibility extends Command
{
    protected $signature   = 'debug:eligibility {email?}';
    protected $description = 'Debug student eligibility data from Firebase';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        $snapshot = $this->db->getReference('users')->getSnapshot();

        if (!$snapshot->exists() || !$snapshot->getValue()) {
            $this->error('No users found in Firebase.');
            return;
        }

        $users = collect($snapshot->getValue())->filter(fn($u) => empty($u['is_deleted']));
        $email = $this->argument('email');

        $this->info('=== ALL STUDENTS IN FIREBASE ===');
        $this->newLine();

        $rows = [];
        foreach ($users as $key => $user) {
            if (($user['role'] ?? '') !== 'student') continue;
            if ($email && strtolower($user['email'] ?? '') !== strtolower($email)) continue;

            $verified = $user['email_verified_at'] ?? null;
            $rows[] = [
                $key,   // full key
                $user['first_name'] ?? '?',
                $user['email'] ?? '?',
                $user['student_id'] ?? '?',
                $verified ? '✓ ' . $verified : '✗ NOT VERIFIED',
            ];
        }

        $this->table(
            ['Firebase Key (FULL)', 'First', 'Email', 'Student ID', 'email_verified_at'],
            $rows
        );

        // Now try to manually write email_verified_at for the test account
        if ($email) {
            $this->newLine();
            $this->info("Attempting to manually set email_verified_at for: {$email}");

            foreach ($users as $key => $user) {
                if (strtolower($user['email'] ?? '') === strtolower($email)) {
                    $now = \Carbon\Carbon::now()->toDateTimeLocalString();
                    $this->db->getReference('users/' . $key)->update([
                        'email_verified_at' => $now,
                    ]);
                    $this->info("✓ Written to users/{$key} → email_verified_at = {$now}");

                    // Verify it was written
                    $check = $this->db->getReference('users/' . $key . '/email_verified_at')->getValue();
                    $this->info("✓ Verified read-back: " . ($check ?? 'NULL - WRITE FAILED'));
                    break;
                }
            }
        }
    }
}
