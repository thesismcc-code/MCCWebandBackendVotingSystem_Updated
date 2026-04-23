<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class ForceDeleteAccounts extends Command
{
    protected $signature = 'accounts:force-delete';
    protected $description = 'Force delete specific accounts by scanning all possible variations';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Force deleting unwanted accounts...');
        $this->newLine();

        $usersRef = $db->getReference('users');
        $snapshot = $usersRef->getSnapshot();

        if (!$snapshot->exists()) {
            $this->error('No users found in database');
            return;
        }

        $users = $snapshot->getValue() ?? [];
        
        // List of emails and names to delete
        $emailsToDelete = [
            'admin@thsismcc.com',
            'admin@thesismcc.com',
            'paulquizon@thesismcc.com',
        ];

        $namesToDelete = [
            'System Administrator',
            'Admin User',
            'Paul Quizon',
        ];

        $deletedCount = 0;
        $accountsFound = [];

        // First pass: Find all matching accounts
        foreach ($users as $userId => $userData) {
            $email = strtolower($userData['email'] ?? '');
            $firstName = $userData['first_name'] ?? '';
            $lastName = $userData['last_name'] ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            $role = $userData['role'] ?? '';
            
            $shouldDelete = false;
            $reason = '';
            
            // Check email match
            foreach ($emailsToDelete as $emailToDelete) {
                if (strtolower($emailToDelete) === $email) {
                    $shouldDelete = true;
                    $reason = "Email matches: {$emailToDelete}";
                    break;
                }
            }
            
            // Check name match
            if (!$shouldDelete) {
                foreach ($namesToDelete as $nameToDelete) {
                    if ($fullName === $nameToDelete || $firstName === $nameToDelete) {
                        $shouldDelete = true;
                        $reason = "Name matches: {$nameToDelete}";
                        break;
                    }
                }
            }
            
            // Check if it's an admin role with suspicious email
            if (!$shouldDelete && $role === 'admin' && (
                str_contains($email, 'thsismcc') || 
                str_contains($email, 'thesismcc')
            )) {
                $shouldDelete = true;
                $reason = "Admin account with matching domain";
            }
            
            if ($shouldDelete) {
                $accountsFound[] = [
                    'userId' => $userId,
                    'name' => $fullName,
                    'email' => $email,
                    'role' => $role,
                    'reason' => $reason,
                ];
            }
        }

        // Show what we found
        if (empty($accountsFound)) {
            $this->info('✅ No matching accounts found - database is already clean!');
            return;
        }

        $this->warn('Found ' . count($accountsFound) . ' accounts to delete:');
        foreach ($accountsFound as $account) {
            $this->line("  - {$account['name']} ({$account['email']}) - {$account['role']}");
            $this->line("    Reason: {$account['reason']}");
        }
        $this->newLine();

        // Delete them
        foreach ($accountsFound as $account) {
            $usersRef->getChild($account['userId'])->remove();
            
            $this->info("✅ Deleted:");
            $this->line("   Name: {$account['name']}");
            $this->line("   Email: {$account['email']}");
            $this->line("   Role: {$account['role']}");
            $this->line("   User ID: {$account['userId']}");
            $this->newLine();
            
            $deletedCount++;
        }

        $this->info("✅ Total accounts deleted: {$deletedCount}");
        
        // Show final count
        $this->newLine();
        $finalSnapshot = $usersRef->getSnapshot();
        if ($finalSnapshot->exists()) {
            $remainingUsers = $finalSnapshot->getValue() ?? [];
            $this->info("Remaining accounts: " . count($remainingUsers));
            
            // Count by role
            $adminCount = 0;
            $comelecCount = 0;
            $saoCount = 0;
            
            foreach ($remainingUsers as $user) {
                $role = $user['role'] ?? '';
                if ($role === 'admin') $adminCount++;
                if ($role === 'comelec') $comelecCount++;
                if ($role === 'sao') $saoCount++;
            }
            
            $this->line("  Admin: {$adminCount}");
            $this->line("  COMELEC: {$comelecCount}");
            $this->line("  SAO: {$saoCount}");
        }
    }
}