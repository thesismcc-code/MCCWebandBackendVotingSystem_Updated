<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class FirebaseSummary extends Command
{
    protected $signature = 'firebase:summary';
    protected $description = 'Show Firebase data summary after cleanup';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('📊 Firebase Data Summary After Cleanup');
        $this->line('==========================================');
        $this->newLine();

        // Check users (for total registered voters)
        $usersRef = $db->getReference('users');
        $usersSnapshot = $usersRef->getSnapshot();
        $totalUsers = 0;
        $studentUsers = 0;
        
        if ($usersSnapshot->exists()) {
            $users = $usersSnapshot->getValue();
            $totalUsers = count($users);
            
            foreach ($users as $user) {
                if (isset($user['role']) && $user['role'] === 'student') {
                    $studentUsers++;
                }
            }
        }

        $this->info("👥 TOTAL REGISTERED VOTERS: {$studentUsers} students (out of {$totalUsers} total users)");
        $this->newLine();

        // Check candidates
        $candidatesRef = $db->getReference('candidates');
        $candidatesSnapshot = $candidatesRef->getSnapshot();
        
        if ($candidatesSnapshot->exists()) {
            $candidates = $candidatesSnapshot->getValue();
            $this->info("🏃 RUNNING CANDIDATES: " . count($candidates));
            
            foreach ($candidates as $id => $candidate) {
                $name = $candidate['full_name'] ?? $candidate['first_name'] . ' ' . $candidate['last_name'] ?? $id;
                $position = $candidate['position'] ?? 'Unknown Position';
                $this->line("  - {$name} ({$position})");
            }
        } else {
            $this->info("🏃 RUNNING CANDIDATES: 0");
        }
        $this->newLine();

        // Check votes
        $votesRef = $db->getReference('votes');
        $votesSnapshot = $votesRef->getSnapshot();
        
        if ($votesSnapshot->exists()) {
            $votes = $votesSnapshot->getValue();
            $this->info("🗳️  LIVE VOTES CAST: " . count($votes));
            
            // Count votes by candidate
            $votesByCandidate = [];
            foreach ($votes as $vote) {
                $candidateId = $vote['candidate_id'] ?? 'unknown';
                $votesByCandidate[$candidateId] = ($votesByCandidate[$candidateId] ?? 0) + 1;
            }
            
            foreach ($votesByCandidate as $candidateId => $voteCount) {
                $this->line("  - {$candidateId}: {$voteCount} votes");
            }
        } else {
            $this->info("🗳️  LIVE VOTES CAST: 0");
        }
        $this->newLine();

        // Calculate turnout
        if ($studentUsers > 0 && $votesSnapshot->exists()) {
            $totalVotes = count($votesSnapshot->getValue());
            $turnout = round(($totalVotes / $studentUsers) * 100, 2);
            $this->info("📈 TURNOUT RATE: {$turnout}%");
        } else {
            $this->info("📈 TURNOUT RATE: 0%");
        }
        $this->newLine();

        $this->info('✅ Cleanup Summary:');
        $this->line('- Removed all votes except for cortesjamesmichael');
        $this->line('- Removed all candidates except cortesjamesmichael');
        $this->line('- Preserved all user accounts for voter registration count');
        $this->line('- Preserved election settings and other system data');
    }
}