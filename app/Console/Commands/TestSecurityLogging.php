<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SecurityLogger;

class TestSecurityLogging extends Command
{
    protected $signature = 'test:security-logging';
    protected $description = 'Test security logging functionality';

    public function handle(SecurityLogger $securityLogger): void
    {
        $this->info('Testing Security Logging...');
        $this->newLine();

        // Test 1: Log a failed login
        $this->line('Test 1: Logging failed login attempt...');
        $securityLogger->logFailedLogin('test@example.com', 'Invalid password');
        $this->info('✅ Failed login logged');

        // Test 2: Log duplicate vote attempt
        $this->line('Test 2: Logging duplicate vote attempt...');
        $securityLogger->logDuplicateVoteAttempt('CS-2025-001', 'Test Student');
        $this->info('✅ Duplicate vote attempt logged');

        // Test 3: Log rejected fingerprint
        $this->line('Test 3: Logging rejected fingerprint...');
        $securityLogger->logRejectedFingerprint('IT-2025-002', 'Another Student', 'Poor scan quality');
        $this->info('✅ Rejected fingerprint logged');

        // Test 4: Log denied access
        $this->line('Test 4: Logging denied access...');
        $securityLogger->logDeniedAccess('BA-2025-003', 'Third Student', 'Not eligible to vote');
        $this->info('✅ Denied access logged');

        $this->newLine();
        $this->info('All security logging tests completed!');
        $this->line('Visit /security-logs to see the logged events');
    }
}