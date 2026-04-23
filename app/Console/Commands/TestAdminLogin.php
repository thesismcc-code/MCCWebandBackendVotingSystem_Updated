<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\User\UserRepository;
use Illuminate\Support\Facades\Hash;

class TestAdminLogin extends Command
{
    protected $signature = 'admin:test-login {email} {password}';
    protected $description = 'Test admin login credentials';

    public function handle(UserRepository $userRepository): void
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("Testing login for: {$email}");
        $this->newLine();

        // Try to find user by email
        $user = $userRepository->findByEmail($email);

        if (!$user) {
            $this->error("❌ User not found with email: {$email}");
            return;
        }

        $this->info("✅ User found:");
        $this->line("  ID: " . $user->getId());
        $this->line("  Email: " . $user->getEmail());
        $this->line("  Role: " . $user->getRole());
        $this->line("  First Name: " . $user->getFirstName());
        $this->line("  Last Name: " . $user->getLastName());
        $this->newLine();

        // Test password
        $storedPassword = $user->getPassword();
        $this->line("Stored password hash: " . substr($storedPassword, 0, 50) . "...");
        
        if (Hash::check($password, $storedPassword)) {
            $this->info("✅ Password matches!");
        } else {
            $this->error("❌ Password does not match!");
            $this->line("Testing with plain text comparison...");
            if ($password === $storedPassword) {
                $this->error("❌ Password is stored as plain text (security issue!)");
            }
        }
    }
}