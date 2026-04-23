<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class DiagnoseSecurityLogs extends Command
{
    protected $signature = 'diagnose:security-logs';
    protected $description = 'Diagnose security logs setup';

    public function handle(): void
    {
        $this->info('🔍 Diagnosing Security Logs Setup...');
        $this->newLine();

        // Check 1: View file exists
        $viewPath = resource_path('views/security-logs.blade.php');
        if (File::exists($viewPath)) {
            $this->info('✅ View file exists: security-logs.blade.php');
        } else {
            $this->error('❌ View file NOT found: security-logs.blade.php');
        }

        // Check 2: Controller exists
        $controllerPath = app_path('Http/Controllers/SecurityLogsController.php');
        if (File::exists($controllerPath)) {
            $this->info('✅ Controller exists: SecurityLogsController.php');
        } else {
            $this->error('❌ Controller NOT found: SecurityLogsController.php');
        }

        // Check 3: Route exists
        $routes = Route::getRoutes();
        $securityLogRoute = null;
        foreach ($routes as $route) {
            if ($route->getName() === 'view.security-logs') {
                $securityLogRoute = $route;
                break;
            }
        }

        if ($securityLogRoute) {
            $this->info('✅ Route registered: view.security-logs');
            $this->line('   URI: ' . $securityLogRoute->uri());
            $this->line('   Methods: ' . implode('|', $securityLogRoute->methods()));
            $this->line('   Action: ' . $securityLogRoute->getActionName());
        } else {
            $this->error('❌ Route NOT registered: view.security-logs');
        }

        // Check 4: Middleware configuration
        $middlewarePath = app_path('Http/Middleware/SessionAuthMiddleware.php');
        if (File::exists($middlewarePath)) {
            $content = File::get($middlewarePath);
            if (str_contains($content, "'security-logs'")) {
                $this->info('✅ Middleware includes security-logs');
            } else {
                $this->error('❌ Middleware does NOT include security-logs');
            }
        }

        // Check 5: Generate URL
        try {
            $url = route('view.security-logs');
            $this->info('✅ Generated URL: ' . $url);
        } catch (\Exception $e) {
            $this->error('❌ Failed to generate URL: ' . $e->getMessage());
        }

        // Check 6: Voting logs view
        $votingLogsPath = resource_path('views/votinglogs.blade.php');
        if (File::exists($votingLogsPath)) {
            $content = File::get($votingLogsPath);
            if (str_contains($content, "route('view.security-logs')")) {
                $this->info('✅ Voting logs view has security-logs link');
            } else {
                $this->error('❌ Voting logs view missing security-logs link');
            }
        }

        $this->newLine();
        $this->info('📋 Diagnosis Complete!');
        $this->newLine();
        $this->info('Next Steps:');
        $this->line('1. Login to your admin account');
        $this->line('2. Navigate to Voting Logs page');
        $this->line('3. Click the Security Logs card');
        $this->line('4. You should see the Security Logs page');
    }
}