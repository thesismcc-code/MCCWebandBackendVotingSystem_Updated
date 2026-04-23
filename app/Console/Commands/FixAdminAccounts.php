<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;

class FixAdminAccounts extends Command
{
    protected $signature = 'admin:fix';
    protected $description = 'Fix admin accounts with proper password hashing';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $accounts = [
            [
                'role' => 'admin',
                'email' => 'admin@thesismcc.com',
                'password' => 'password123',
                'first_name' => 'Admin',
                'last_name' => 'User'
            ],
            [
                'role' => 'sao',
                'email' => 'sao@thesismcc.com',
                'password' => 'password123',
                'first_name' => 'SAO',
                'last_name' => 'User'
            ],
            [
                'role' => 'comelec',
                'email' => 'comelec@thesismcc.com',
                'password' => 'password123',
                'first_name' => 'COMELEC',
                'last_name' => 'User'
            ]
        ];

        $this->info('Fixing admin accounts in Firebase...');
        $this->newLine();

        // First, let's clear any existing accounts with these emails
        $usersRef = $db->getReference('users');
        $snapshot = $usersRef->getSnapshot();
        
        if ($snapshot->exists()) {
            foreach ($snapshot->getValue() as $key => $userData) {
                if (isset($userData['email']) && in_array($userData['email'], ['admin@thesismcc.com', 'sao@thesismcc.com', 'comelec@thesismcc.com'])) {
                    $usersRef->getChild($key)->remove();
                    $this->line("Removed existing account: {$userData['email']}");
                }
            }
        }

        foreach ($accounts as $account) {
            $userId = strtoupper($account['role']) . '_' . Str::random(8);
            
            // Hash the password properly
            $hashedPassword = Hash::make($account['password']);
            
            $userData = [
                'id' => $userId,
                'first_name' => $account['first_name'],
                'middle_name' => '',
                'last_name' => $account['last_name'],
                'email' => $account['email'],
                'password' => $hashedPassword,
                'role' => $account['role'],
                'student_id' => null,
                'admin_id' => $account['role'] === 'admin' ? 'ADM-' . date('Y') . '-001' : null,
                'comelec_id' => $account['role'] === 'comelec' ? 'COM-' . date('Y') . '-001' : null,
                'sao_id' => $account['role'] === 'sao' ? 'SAO-' . date('Y') . '-001' : null,
                'email_verified_at' => now()->toDateTimeLocalString(),
                'is_deleted' => false,
                'created_at' => now()->toDateTimeLocalString(),
                'updated_at' => now()->toDateTimeLocalString(),
            ];

            $db->getReference('users/' . $userId)->set($userData);

            $this->info("✅ Fixed {$account['role']} account:");
            $this->line("  Firebase Key: {$userId}");
            $this->line("  Email: {$account['email']}");
            $this->line("  Password: {$account['password']}");
            $this->line("  Role: {$account['role']}");
            $this->line("  Password Hash: " . substr($hashedPassword, 0, 30) . "...");
            $this->newLine();
        }

        $this->info('All admin accounts fixed successfully!');
    }
}