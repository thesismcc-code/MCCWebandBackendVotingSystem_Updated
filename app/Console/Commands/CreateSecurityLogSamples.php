<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class CreateSecurityLogSamples extends Command
{
    protected $signature = 'security-logs:create-samples';
    protected $description = 'Create sample security log data for testing';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Creating sample security log data...');
        $this->newLine();

        $sampleLogs = [
            [
                'id' => 'SEC_' . uniqid(),
                'type' => 'duplicate_vote_attempt',
                'student_id' => 'CS-2025-001',
                'name' => 'Jose Perolino',
                'description' => 'Attempted to vote multiple times',
                'first_attempt' => '2025-04-15 10:43:00',
                'second_attempt' => '2025-04-15 10:43:30',
                'created_at' => '2025-04-15 10:43:30',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0...',
            ],
            [
                'id' => 'SEC_' . uniqid(),
                'type' => 'rejected_fingerprint',
                'student_id' => 'IT-2025-035',
                'name' => 'Myles Morohon',
                'description' => 'Fingerprint scan rejected - poor quality',
                'first_attempt' => '2025-04-15 13:00:00',
                'second_attempt' => '2025-04-15 13:00:15',
                'created_at' => '2025-04-15 13:00:15',
                'ip_address' => '192.168.1.101',
            ],
            [
                'id' => 'SEC_' . uniqid(),
                'type' => 'denied_access',
                'student_id' => 'BA-2025-141',
                'name' => 'Honey Malang',
                'description' => 'Access denied - not eligible to vote',
                'first_attempt' => '2025-04-15 08:41:00',
                'created_at' => '2025-04-15 08:41:00',
                'ip_address' => '192.168.1.102',
            ],
            [
                'id' => 'SEC_' . uniqid(),
                'type' => 'duplicate_vote_attempt',
                'student_id' => 'CS-2025-225',
                'name' => 'Jahaira Ampaso',
                'description' => 'Attempted to vote again after successful vote',
                'first_attempt' => '2025-04-15 10:43:00',
                'second_attempt' => '2025-04-15 10:43:30',
                'created_at' => '2025-04-15 10:43:30',
                'ip_address' => '192.168.1.103',
            ],
        ];

        $securityLogsRef = $db->getReference('security_logs');
        
        foreach ($sampleLogs as $log) {
            $securityLogsRef->push($log);
            $this->line("✅ Created security log: {$log['type']} for {$log['name']}");
        }

        $this->newLine();
        $this->info('Sample security logs created successfully!');
        $this->line('Total logs created: ' . count($sampleLogs));
        $this->newLine();
        $this->info('You can now visit the Security Logs page to see the data.');
    }
}