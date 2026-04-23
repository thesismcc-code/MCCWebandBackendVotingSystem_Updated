<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class ClearCandidates extends Command
{
    protected $signature = 'candidates:clear';
    protected $description = 'Remove all candidates from Firebase';

    private Database $db;

    public function __construct(Database $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    public function handle()
    {
        $this->info('Fetching candidates from Firebase...');
        
        $candidatesRef = $this->db->getReference('candidates');
        $snapshot = $candidatesRef->getSnapshot();
        
        if (!$snapshot->exists() || $snapshot->getValue() === null) {
            $this->info('No candidates found in Firebase.');
            return 0;
        }

        $candidates = $snapshot->getValue();
        $count = count($candidates);
        
        $this->info("Found {$count} candidate(s) in Firebase:");
        
        foreach ($candidates as $candId => $candData) {
            $name = $candData['name'] ?? $candData['candidate_name'] ?? 'Unknown';
            $this->line("  - {$name} (ID: {$candId})");
        }
        
        $this->newLine();
        
        if ($this->confirm('Do you want to delete all candidates?', true)) {
            $this->info('Deleting all candidates...');
            
            $candidatesRef->remove();
            
            $this->newLine();
            $this->info("✓ Successfully removed {$count} candidate(s) from Firebase!");
            $this->info('✓ Total Candidates is now: 0');
        } else {
            $this->info('Operation cancelled. No candidates were deleted.');
        }
        
        return 0;
    }
}
