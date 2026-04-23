<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class CheckVerifiedStudents extends Command
{
    protected $signature   = 'check:verified';
    protected $description = 'Show all students and their email_verified_at from Firebase';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        $snapshot = $this->db->getReference('users')->getSnapshot();

        if (!$snapshot->exists() || !$snapshot->getValue()) {
            $this->error('No users in Firebase.');
            return;
        }

        $rows = [];
        $eligible = 0;
        $total = 0;

        foreach ($snapshot->getValue() as $key => $user) {
            if (($user['role'] ?? '') !== 'student') continue;
            if (!empty($user['is_deleted'])) continue;

            $total++;
            $verified = $user['email_verified_at'] ?? null;
            if ($verified) $eligible++;

            $rows[] = [
                $key,
                ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
                $user['email'] ?? '',
                $user['student_id'] ?? '',
                $verified ?: '— NOT SET',
            ];
        }

        $this->table(
            ['Firebase Key', 'Name', 'Email', 'Student ID', 'email_verified_at'],
            $rows
        );

        $this->newLine();
        $this->info("Total students : {$total}");
        $this->info("Eligible       : {$eligible}");
        $this->info("Not eligible   : " . ($total - $eligible));
    }
}
