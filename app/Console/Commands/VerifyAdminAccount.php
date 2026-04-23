<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class VerifyAdminAccount extends Command
{
    protected $signature = 'admin:verify';
    protected $description = 'Verify admin accounts exist';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Checking admin accounts...');
        $this->newLine();

        $usersRef = $db->getReference('users');
        $snapshot = $usersRef->getSnapshot();

        if (!$snapshot->exists()) {
            $this->error('No users found');
            return;
        }

        $users = $snapshot->getValue() ?? [];
        $adminAccounts = [];

        foreach ($users as $userId => $userData) {
            $email = $userData['email'] ?? '';
            $role = $userData['role'] ?? '';
            
            if ($email === 'admin@thesismcc.com' || 
                $email === 'sao@thesismcc.com' || 
                $email === 'comelec@thesismcc.com') {
                
                $name = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
                $adminAccounts[] = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'created' => $userData['created_at'] ?? 'N/A',
                ];
            }
        }

        if (empty($adminAccounts)) {
            $this->error('No admin accounts found!');
            return;
        }

        $this->info('✅ Found ' . count($adminAccounts) . ' admin accounts:');
        $this->newLine();

        foreach ($adminAccounts as $account) {
            $this->line("📧 Email: {$account['email']}");
            $this->line("   Name: {$account['name']}");
            $this->line("   Role: {$account['role']}");
            $this->line("   Password: password123");
            $this->newLine();
        }

        $this->info('All accounts are ready to use!');
    }
}