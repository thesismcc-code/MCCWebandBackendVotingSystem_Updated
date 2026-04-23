<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class CleanFirebaseData extends Command
{
    protected $signature = 'firebase:clean';
    protected $description = 'Clean Firebase data - remove live votes and candidates except cortesjamesmichael';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Cleaning Firebase data...');
        $this->newLine();

        // First, let's see what collections exist
        $this->info('Current Firebase structure:');
        $rootSnapshot = $db->getReference()->getSnapshot();
        
        if ($rootSnapshot->exists()) {
            foreach ($rootSnapshot->getValue() as $collection => $data) {
                $this->line("- {$collection}");
                if (is_array($data)) {
                    $count = count($data);
                    $this->line("  ({$count} items)");
                }
            }
        }
        $this->newLine();

        // Clean votes collection - keep only cortesjamesmichael votes
        $this->info('Cleaning votes collection...');
        $votesRef = $db->getReference('votes');
        $votesSnapshot = $votesRef->getSnapshot();
        
        if ($votesSnapshot->exists()) {
            $votesData = $votesSnapshot->getValue();
            $preservedVotes = [];
            $removedCount = 0;
            
            foreach ($votesData as $voteId => $voteData) {
                if (isset($voteData['candidate_id']) && $voteData['candidate_id'] === 'cortesjamesmichael') {
                    $preservedVotes[$voteId] = $voteData;
                    $this->line("✅ Preserved vote for cortesjamesmichael: {$voteId}");
                } else {
                    $removedCount++;
                }
            }
            
            // Clear all votes and set only preserved ones
            $votesRef->set($preservedVotes);
            $this->info("Removed {$removedCount} votes, preserved " . count($preservedVotes) . " votes for cortesjamesmichael");
        } else {
            $this->line("No votes collection found");
        }
        $this->newLine();

        // Clean candidates collection - remove all except cortesjamesmichael
        $this->info('Cleaning candidates collection...');
        $candidatesRef = $db->getReference('candidates');
        $candidatesSnapshot = $candidatesRef->getSnapshot();
        
        if ($candidatesSnapshot->exists()) {
            $candidatesData = $candidatesSnapshot->getValue();
            $preservedCandidates = [];
            $removedCount = 0;
            
            foreach ($candidatesData as $candidateId => $candidateData) {
                if ($candidateId === 'cortesjamesmichael' || 
                    (isset($candidateData['id']) && $candidateData['id'] === 'cortesjamesmichael') ||
                    (isset($candidateData['candidate_id']) && $candidateData['candidate_id'] === 'cortesjamesmichael')) {
                    $preservedCandidates[$candidateId] = $candidateData;
                    $this->line("✅ Preserved candidate: {$candidateId}");
                } else {
                    $removedCount++;
                }
            }
            
            // Clear all candidates and set only preserved ones
            $candidatesRef->set($preservedCandidates);
            $this->info("Removed {$removedCount} candidates, preserved " . count($preservedCandidates) . " candidates");
        } else {
            $this->line("No candidates collection found");
        }
        $this->newLine();

        // Clean results collection if it exists
        $this->info('Cleaning results collection...');
        $resultsRef = $db->getReference('results');
        $resultsSnapshot = $resultsRef->getSnapshot();
        
        if ($resultsSnapshot->exists()) {
            $resultsData = $resultsSnapshot->getValue();
            $preservedResults = [];
            $removedCount = 0;
            
            foreach ($resultsData as $resultId => $resultData) {
                if (isset($resultData['candidate_id']) && $resultData['candidate_id'] === 'cortesjamesmichael') {
                    $preservedResults[$resultId] = $resultData;
                    $this->line("✅ Preserved result for cortesjamesmichael: {$resultId}");
                } else {
                    $removedCount++;
                }
            }
            
            // Clear all results and set only preserved ones
            $resultsRef->set($preservedResults);
            $this->info("Removed {$removedCount} results, preserved " . count($preservedResults) . " results for cortesjamesmichael");
        } else {
            $this->line("No results collection found");
        }
        $this->newLine();

        // Keep users collection intact (for total registered voters)
        $this->info('Users collection preserved for total registered voters count');
        
        $this->info('✅ Firebase data cleanup completed!');
        $this->info('Preserved:');
        $this->line('- Total registered voters (users collection)');
        $this->line('- Votes for cortesjamesmichael only');
        $this->line('- Candidate data for cortesjamesmichael only');
    }
}