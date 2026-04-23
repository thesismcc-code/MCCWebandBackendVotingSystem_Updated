<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class VerifyFirebaseAccounts extends Command
{
    protected $signature = 'accounts:verify';
    protected $description = 'Verify current accounts in Firebase';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Current accounts in Firebase:');
        $this->newLine();

        $usersRef = $db->getReference('users');
        $snapshot = $usersRef->getSnapshot();

        if (!$snapshot->exists()) {
            $this->error('No users found in database');
            return;
        }

        $users = $snapshot->getValue() ?? [];
        
        $adminCount = 0;
        $comelecCount = 0;
        $saoCount = 0;
        $studentCount = 0;
        
        $this->table(
            ['User ID', 'Name', 'Email', 'Role', 'Created'],
            collect($users)->map(function ($userData, $userId) use (&$adminCount, &$comelecCount, &$saoCount, &$studentCount) {
                $name = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
                $email = $userData['email'] ?? '';
                $role = $userData['role'] ?? '';
                $created = $userData['created_at'] ?? '';
                
                // Count by role
                if ($role === 'admin') $adminCount++;
                if ($role === 'comelec') $comelecCount++;
                if ($role === 'sao') $saoCount++;
                if ($role === 'student') $studentCount++;
                
                return [
                    substr($userId, 0, 15) . '...',
                    $name,
                    $email,
                    $role,
                    substr($created, 0, 10)
                ];
            })->values()->toArray()
        );

        $this->newLine();
        $this->info('Summary:');
        $this->line("Total Accounts: " . count($users));
        $this->line("Admin: {$adminCount}");
        $this->line("COMELEC Officers: {$comelecCount}");
        $this->line("SAO Head: {$saoCount}");
        $this->line("Students: {$studentCount}");
    }
}