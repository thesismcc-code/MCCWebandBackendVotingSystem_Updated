<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class CheckYearLevels extends Command
{
    protected $signature = 'check:year-levels';
    protected $description = 'Verify year level data from Firebase matches the display';

    public function __construct(private Database $db) { parent::__construct(); }

    public function handle()
    {
        $users = $this->db->getReference('users')->getValue() ?? [];
        $yearCount = [];
        $totalStudents = 0;

        foreach ($users as $id => $u) {
            if (($u['role'] ?? '') !== 'student') continue;
            $totalStudents++;
            $year = $u['year'] ?? $u['Year'] ?? $u['year_level'] ?? 'unknown';
            $yearCount[$year] = ($yearCount[$year] ?? 0) + 1;
        }

        $this->info("Total students: {$totalStudents}");
        $this->table(['Year Field Value', 'Count'], array_map(
            fn($k, $v) => [$k, $v],
            array_keys($yearCount),
            array_values($yearCount)
        ));

        // Check votes
        $votes = $this->db->getReference('votes')->getValue() ?? [];
        $this->info("Total votes in Firebase: " . count($votes));

        // Check active election
        $elections = $this->db->getReference('elections')->getValue() ?? [];
        foreach ($elections as $id => $e) {
            if (($e['status'] ?? '') === 'active') {
                $this->info("Active election: {$e['election_name']} (ID: {$id})");
            }
        }

        return 0;
    }
}
