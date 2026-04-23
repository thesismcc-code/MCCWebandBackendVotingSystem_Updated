<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class RemoveDuplicatePositions extends Command
{
    protected $signature = 'positions:remove-duplicates';
    protected $description = 'Remove duplicate positions from Firebase, keeping only unique ones';

    private Database $db;

    public function __construct(Database $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    public function handle()
    {
        $this->info('Fetching positions from Firebase...');
        
        $positionsRef = $this->db->getReference('positions');
        $snapshot = $positionsRef->getSnapshot();
        
        if (!$snapshot->exists() || $snapshot->getValue() === null) {
            $this->info('No positions found in Firebase.');
            return 0;
        }

        $positions = $snapshot->getValue();
        $this->info('Found ' . count($positions) . ' total positions.');
        
        $uniquePositions = [];
        $toDelete = [];
        
        foreach ($positions as $posId => $posData) {
            $positionName = $posData['position_name'] 
                ?? $posData['name'] 
                ?? $posData['positionName'] 
                ?? 'Unknown';
                
            $maxVote = $posData['max_vote'] ?? $posData['max_votes'] ?? 1;
            $uniqueKey = strtolower(trim($positionName)) . '_' . $maxVote;
            
            if (!isset($uniquePositions[$uniqueKey])) {
                $uniquePositions[$uniqueKey] = ['id' => $posId, 'name' => $positionName];
                $this->line("✓ Keeping: {$positionName} - ID: {$posId}");
            } else {
                $toDelete[] = $posId;
                $this->warn("✗ Duplicate: {$positionName} - ID: {$posId}");
            }
        }
        
        if (empty($toDelete)) {
            $this->info('No duplicates found!');
            return 0;
        }
        
        foreach ($toDelete as $posId) {
            $positionsRef->getChild($posId)->remove();
        }
        
        $this->info('Removed ' . count($toDelete) . ' duplicates.');
        return 0;
    }
}
