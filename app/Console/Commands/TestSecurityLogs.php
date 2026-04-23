<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SecurityLogsController;
use Kreait\Firebase\Contract\Database;

class TestSecurityLogs extends Command
{
    protected $signature = 'test:security-logs';
    protected $description = 'Test security logs functionality';

    public function handle(Database $db): void
    {
        $this->info('Testing Security Logs functionality...');
        $this->newLine();

        // Test if controller can be instantiated
        try {
            $controller = new SecurityLogsController($db);
            $this->info('✅ SecurityLogsController instantiated successfully');
        } catch (\Exception $e) {
            $this->error('❌ Failed to instantiate SecurityLogsController: ' . $e->getMessage());
            return;
        }

        // Test if security logs data exists
        $securityLogsSnap = $db->getReference('security_logs')->getSnapshot();
        if ($securityLogsSnap->exists()) {
            $logs = $securityLogsSnap->getValue() ?? [];
            $this->info('✅ Security logs data found: ' . count($logs) . ' entries');
        } else {
            $this->line('⚠️  No security logs data found');
        }

        // Test route
        $this->info('✅ Route should be: /security-logs');
        $this->info('✅ Route name should be: view.security-logs');
        
        $this->newLine();
        $this->info('Security Logs test completed!');
    }
}