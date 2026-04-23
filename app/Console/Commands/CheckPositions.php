<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class CheckPositions extends Command
{
    protected $signature   = 'check:positions';
    protected $description = 'Show all positions in Firebase';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        $snap = $this->db->getReference('positions')->getSnapshot();

        if (!$snap->exists() || !$snap->getValue()) {
            $this->warn('No positions found in Firebase.');
            return;
        }

        $rows = [];
        foreach ($snap->getValue() as $id => $p) {
            $rows[] = [$id, $p['position_name'] ?? $p['name'] ?? '?', $p['max_vote'] ?? $p['max_votes'] ?? '?'];
        }

        $this->table(['Firebase Key', 'Position Name', 'Max Vote'], $rows);
    }
}
