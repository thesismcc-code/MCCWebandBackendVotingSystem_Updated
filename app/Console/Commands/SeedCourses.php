<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class SeedCourses extends Command
{
    protected $signature = 'courses:seed';
    protected $description = 'Seed courses in Firebase Realtime Database';

    private Database $db;

    public function __construct(Database $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    public function handle()
    {
        $this->info('Seeding courses to Firebase...');
        
        $coursesRef = $this->db->getReference('courses');
        
        // Check if courses already exist
        $snapshot = $coursesRef->getSnapshot();
        if ($snapshot->exists() && $snapshot->getValue() !== null) {
            if (!$this->confirm('Courses already exist. Do you want to overwrite them?', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }
            // Clear existing courses
            $coursesRef->remove();
        }
        
        // Default courses for engineering college
        $courses = [
            ['course_name' => 'BSIT', 'full_name' => 'Bachelor of Science in Information Technology'],
            ['course_name' => 'BSCS', 'full_name' => 'Bachelor of Science in Computer Science'],
            ['course_name' => 'BSCE', 'full_name' => 'Bachelor of Science in Civil Engineering'],
            ['course_name' => 'BSEE', 'full_name' => 'Bachelor of Science in Electrical Engineering'],
            ['course_name' => 'BSME', 'full_name' => 'Bachelor of Science in Mechanical Engineering'],
            ['course_name' => 'BSCpE', 'full_name' => 'Bachelor of Science in Computer Engineering'],
            ['course_name' => 'BSECE', 'full_name' => 'Bachelor of Science in Electronics and Communications Engineering'],
        ];
        
        foreach ($courses as $course) {
            $coursesRef->push([
                'course_name' => $course['course_name'],
                'full_name' => $course['full_name'],
                'created_at' => now()->toIso8601String(),
            ]);
            $this->line("✓ Added: {$course['course_name']} - {$course['full_name']}");
        }
        
        $this->newLine();
        $this->info('✓ Successfully seeded ' . count($courses) . ' courses to Firebase!');
        
        return 0;
    }
}
