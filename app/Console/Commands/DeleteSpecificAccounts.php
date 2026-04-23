<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class DeleteSpecificAccounts extends Command
{
    protected $signature = 'accounts:delete-specific';
    protected $description = 'Delete specific accounts from Firebase';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Searching for accounts to delete...');
        $this->newLine();

        $usersRef = $db->getReference('users');
        $snapshot = $usersRef->getSnapshot();

        if (!$snapshot->exists()) {
            $this->error('No users found in database');
            return;
        }

        $users = $snapshot->getValue() ?? [];
        $emailsToDelete = [
            'admin@thsismcc.com',  // Note: typo in original (thsismcc instead of thesismcc)
            'paulquizon@thesismcc.com'
        ];

        $deletedCount = 0;

        foreach ($users as $userId => $userData) {
            $email = $userData['email'] ?? '';
            
            if (in_array($email, $emailsToDelete)) {
                $name = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
                $role = $userData['role'] ?? 'unknown';
                
                // Delete the user
                $usersRef->getChild($userId)->remove();
                
                $this->info("✅ Deleted account:");
                $this->line("   Name: {$name}");
                $this->line("   Email: {$email}");
                $this->line("   Role: {$role}");
                $this->line("   User ID: {$userId}");
                $this->newLine();
                
                $deletedCount++;
            }
        }

        if ($deletedCount === 0) {
            $this->warn('No matching accounts found to delete');
            $this->line('Searched for:');
            foreach ($emailsToDelete as $email) {
                $this->line("  - {$email}");
            }
        } else {
            $this->info("Total accounts deleted: {$deletedCount}");
        }
    }
}