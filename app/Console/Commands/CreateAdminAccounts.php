<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;

class CreateAdminAccounts extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create admin accounts (admin, sao, comelec) in Firebase';

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
                'first_name' => 'System',
                'last_name' => 'Administrator'
            ],
            [
                'role' => 'sao',
                'email' => 'sao@thesismcc.com',
                'password' => 'password123',
                'first_name' => 'Student Affairs',
                'last_name' => 'Officer'
            ],
            [
                'role' => 'comelec',
                'email' => 'comelec@thesismcc.com',
                'password' => 'password123',
                'first_name' => 'Commission on',
                'last_name' => 'Elections'
            ]
        ];

        $this->info('Creating admin accounts in Firebase...');
        $this->newLine();

        foreach ($accounts as $account) {
            $userId = strtoupper($account['role']) . '_' . Str::random(8);
            
            $userData = [
                'id' => $userId,
                'first_name' => $account['first_name'],
                'middle_name' => '',
                'last_name' => $account['last_name'],
                'email' => $account['email'],
                'password' => Hash::make($account['password']),
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

            $this->info("✓ Created {$account['role']} account:");
            $this->line("  Firebase Key: {$userId}");
            $this->line("  Email: {$account['email']}");
            $this->line("  Password: {$account['password']}");
            $this->line("  Role: {$account['role']}");
            $this->newLine();
        }

        $this->info('All admin accounts created successfully in Firebase!');
    }
}