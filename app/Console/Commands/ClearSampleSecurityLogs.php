<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class ClearSampleSecurityLogs extends Command
{
    protected $signature = 'security-logs:clear-samples';
    protected $description = 'Clear sample/fake security log data';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Clearing sample security log data...');
        $this->newLine();

        $securityLogsRef = $db->getReference('security_logs');
        $snapshot = $securityLogsRef->getSnapshot();
        
        if ($snapshot->exists()) {
            $logs = $snapshot->getValue() ?? [];
            $this->line('Found ' . count($logs) . ' security log entries');
            
            // Clear all security logs
            $securityLogsRef->remove();
            
            $this->info('✅ All security logs cleared');
        } else {
            $this->line('No security logs found');
        }

        $this->newLine();
        $this->info('Security logs will now only contain real events from:');
        $this->line('- Failed login attempts');
        $this->line('- Duplicate vote attempts');
        $this->line('- Rejected fingerprint scans');
        $this->line('- Denied access attempts');
    }
}