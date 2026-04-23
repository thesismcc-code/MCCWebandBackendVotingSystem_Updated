<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckRoutes extends Command
{
    protected $signature = 'check:routes';
    protected $description = 'Check if security logs routes are properly registered';

    public function handle(): void
    {
        $this->info('Checking Security Logs routes...');
        $this->newLine();

        // Get all routes
        $routes = Route::getRoutes();
        
        $securityLogRoutes = [];
        $votingLogRoutes = [];
        
        foreach ($routes as $route) {
            $name = $route->getName();
            $uri = $route->uri();
            
            if (str_contains($uri, 'security-logs') || str_contains($name ?? '', 'security')) {
                $securityLogRoutes[] = [
                    'name' => $name,
                    'uri' => $uri,
                    'methods' => implode('|', $route->methods()),
                ];
            }
            
            if (str_contains($uri, 'voting-logs') || str_contains($name ?? '', 'voting-logs')) {
                $votingLogRoutes[] = [
                    'name' => $name,
                    'uri' => $uri,
                    'methods' => implode('|', $route->methods()),
                ];
            }
        }

        $this->info('Voting Logs Routes:');
        foreach ($votingLogRoutes as $route) {
            $this->line("  {$route['methods']} {$route['uri']} -> {$route['name']}");
        }
        
        $this->newLine();
        $this->info('Security Logs Routes:');
        foreach ($securityLogRoutes as $route) {
            $this->line("  {$route['methods']} {$route['uri']} -> {$route['name']}");
        }

        $this->newLine();
        
        // Test route generation
        try {
            $securityLogsUrl = route('view.security-logs');
            $this->info("✅ Security logs URL: {$securityLogsUrl}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate security logs URL: " . $e->getMessage());
        }

        try {
            $votingLogsUrl = route('view.voting-logs');
            $this->info("✅ Voting logs URL: {$votingLogsUrl}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate voting logs URL: " . $e->getMessage());
        }
    }
}