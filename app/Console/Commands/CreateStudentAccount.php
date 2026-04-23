<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;

class CreateStudentAccount extends Command
{
    protected $signature = 'student:create';
    protected $description = 'Create a test student account in Firebase';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $userId    = 'STU' . Str::random(12);
        $studentId = 'STU-2026-001';

        $db->getReference('users/' . $userId)->set([
            'id'                => $userId,
            'first_name'        => 'Juan',
            'middle_name'       => 'Dela',
            'last_name'         => 'Cruz',
            'email'             => 'student@school.edu',
            'password'          => Hash::make(env('DEFAULT_PASSWORD', 'password123')),
            'role'              => 'student',
            'student_id'        => $studentId,
            'admin_id'          => null,
            'comelec_id'        => null,
            'email_verified_at' => null,
            'is_deleted'        => false,
            'created_at'        => now()->toDateTimeLocalString(),
            'updated_at'        => now()->toDateTimeLocalString(),
        ]);

        $this->info('Student account created!');
        $this->line('Firebase Key : ' . $userId);
        $this->line('Student ID   : ' . $studentId);
        $this->line('Email        : student@school.edu');
        $this->line('Password     : password123');
    }
}
