<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SecurityLogger;
use Carbon\Carbon;

class SeedSystemLogs extends Command
{
    protected $signature = 'logs:seed';
    protected $description = 'Seed sample system activity logs for testing';

    public function handle(SecurityLogger $logger)
    {
        $this->info('Seeding system logs...');

        // Sample activities from today
        $activities = [
            ['level' => 'info', 'message' => 'Admin logged in', 'user' => 'Admin', 'time' => now()->subMinutes(30)],
            ['level' => 'info', 'message' => 'Student logged in', 'user' => 'Student', 'time' => now()->subMinutes(25)],
            ['level' => 'info', 'message' => 'Fingerprint scan successful', 'user' => 'Student', 'time' => now()->subMinutes(20)],
            ['level' => 'info', 'message' => 'Added new position: President', 'user' => 'Admin', 'time' => now()->subMinutes(15)],
            ['level' => 'info', 'message' => 'Added candidate: James Michael Cortes', 'user' => 'Comelec', 'time' => now()->subMinutes(10)],
            ['level' => 'info', 'message' => 'Updated election settings', 'user' => 'Admin', 'time' => now()->subMinutes(8)],
            ['level' => 'info', 'message' => 'Student verified fingerprint', 'user' => 'Student', 'time' => now()->subMinutes(5)],
            ['level' => 'error', 'message' => 'Failed fingerprint scan - Poor quality', 'user' => 'Student', 'time' => now()->subMinutes(12)],
            ['level' => 'error', 'message' => 'Login attempt blocked - Invalid credentials', 'user' => 'Student', 'time' => now()->subMinutes(18)],
            ['level' => 'critical', 'message' => 'Database connection timeout', 'user' => 'System', 'time' => now()->subMinutes(22)],
        ];

        // Yesterday's activities
        $yesterdayActivities = [
            ['level' => 'info', 'message' => 'Admin logged in', 'user' => 'Admin', 'time' => now()->subDay()->setTime(9, 30)],
            ['level' => 'info', 'message' => 'Comelec logged in', 'user' => 'Comelec', 'time' => now()->subDay()->setTime(10, 15)],
            ['level' => 'info', 'message' => 'Added new candidate', 'user' => 'Comelec', 'time' => now()->subDay()->setTime(11, 0)],
            ['level' => 'error', 'message' => 'Failed to upload candidate photo', 'user' => 'Comelec', 'time' => now()->subDay()->setTime(11, 5)],
        ];

        // Last week's activities
        $lastWeekActivities = [
            ['level' => 'info', 'message' => 'System backup completed', 'user' => 'System', 'time' => now()->subDays(3)->setTime(2, 0)],
            ['level' => 'info', 'message' => 'Election created: SSG Election 2025', 'user' => 'Admin', 'time' => now()->subDays(5)->setTime(14, 30)],
            ['level' => 'error', 'message' => 'Email notification failed', 'user' => 'System', 'time' => now()->subDays(4)->setTime(16, 45)],
        ];

        $allActivities = array_merge($activities, $yesterdayActivities, $lastWeekActivities);

        foreach ($allActivities as $activity) {
            // Temporarily set the created_at to the specified time
            $logger->log(
                $activity['level'],
                $activity['message'],
                $activity['user'],
                ['seeded' => true, 'original_time' => $activity['time']->toIso8601String()]
            );
        }

        $this->info('✓ Seeded ' . count($allActivities) . ' system logs successfully!');
        $this->info('Visit /system-activity to view the logs');

        return 0;
    }
}
