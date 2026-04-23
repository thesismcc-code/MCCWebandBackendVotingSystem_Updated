<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class CleanupDuplicateAccounts extends Command
{
    protected $signature = 'accounts:cleanup-duplicates';
    protected $description = 'Clean up duplicate and unwanted accounts';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Searching for accounts to clean up...');
        $this->newLine();

        $usersRef = $db->getReference('users');
        $snapshot = $usersRef->getSnapshot();

        if (!$snapshot->exists()) {
            $this->error('No users found in database');
            return;
        }

        $users = $snapshot->getValue() ?? [];
        
        // Accounts to delete - checking by email and name
        $accountsToDelete = [
            'admin@thsismcc.com',
            'admin@thesismcc.com',
            'paulquizon@thesismcc.com',
        ];

        $namesToDelete = [
            'System Administrator',
            'Paul Quizon',
        ];

        $deletedCount = 0;

        foreach ($users as $userId => $userData) {
            $email = $userData['email'] ?? '';
            $firstName = $userData['first_name'] ?? '';
            $lastName = $userData['last_name'] ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            
            // Check if email or name matches
            $shouldDelete = in_array($email, $accountsToDelete) || in_array($fullName, $namesToDelete);
            
            // Also check for "System Administrator" as first name
            if ($firstName === 'System Administrator' || $fullName === 'System Administrator') {
                $shouldDelete = true;
            }
            
            if ($shouldDelete) {
                $role = $userData['role'] ?? 'unknown';
                $createdAt = $userData['created_at'] ?? 'unknown';
                
                // Delete the user
                $usersRef->getChild($userId)->remove();
                
                $this->info("✅ Deleted account:");
                $this->line("   Name: {$fullName}");
                $this->line("   Email: {$email}");
                $this->line("   Role: {$role}");
                $this->line("   Created: {$createdAt}");
                $this->line("   User ID: {$userId}");
                $this->newLine();
                
                $deletedCount++;
            }
        }

        if ($deletedCount === 0) {
            $this->info('✅ No duplicate accounts found - database is clean!');
        } else {
            $this->info("✅ Total accounts deleted: {$deletedCount}");
        }
        
        // Show remaining accounts
        $this->newLine();
        $this->info('Remaining accounts in database:');
        $snapshot = $usersRef->getSnapshot();
        if ($snapshot->exists()) {
            $remainingUsers = $snapshot->getValue() ?? [];
            $this->line('Total: ' . count($remainingUsers) . ' accounts');
            foreach ($remainingUsers as $userId => $userData) {
                $name = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
                $email = $userData['email'] ?? '';
                $role = $userData['role'] ?? '';
                $this->line("  - {$name} ({$email}) - {$role}");
            }
        }
    }
}