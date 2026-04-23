<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class MarkStudentVerified extends Command
{
    protected $signature   = 'student:verify-email {email}';
    protected $description = 'Manually mark a student email as verified in Firebase';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        $email = $this->argument('email');

        $snapshot = $this->db->getReference('users')
            ->orderByChild('email')
            ->equalTo($email)
            ->getSnapshot();

        if (!$snapshot->exists() || !$snapshot->getValue()) {
            $this->error("No user found with email: {$email}");
            return;
        }

        $key  = array_key_first($snapshot->getValue());
        $user = $snapshot->getValue()[$key];

        if (($user['role'] ?? '') !== 'student') {
            $this->error("User {$email} is not a student (role: " . ($user['role'] ?? 'unknown') . ")");
            return;
        }

        $now = Carbon::now()->toDateTimeLocalString();
        $this->db->getReference('users/' . $key)->update([
            'email_verified_at' => $now,
        ]);

        $check = $this->db->getReference('users/' . $key . '/email_verified_at')->getValue();

        $this->info("✓ Marked as verified:");
        $this->info("  Firebase key      : {$key}");
        $this->info("  Name              : " . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        $this->info("  Email             : {$email}");
        $this->info("  email_verified_at : {$check}");
    }
}
