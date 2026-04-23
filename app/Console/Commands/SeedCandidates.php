<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class SeedCandidates extends Command
{
    protected $signature   = 'seed:candidates';
    protected $description = 'Seed 2 presidents, 2 vice presidents, 12 senators for the active election';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        // Get active election
        $electionSnap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();

        if (!$electionSnap->exists() || !$electionSnap->getValue()) {
            $this->error('No active election found. Please create one first.');
            return;
        }

        $electionId = array_key_first($electionSnap->getValue());
        $this->info("Active election: {$electionId}");

        // Assign positions to students
        // 2 Presidents, 2 Vice Presidents, 12 Senators = 16 total
        $assignments = [
            'STUKbgW3ienuGJe' => ['position' => 'President',      'name' => 'James Michael Cortes'],
            'STU1NisakyZkNnw' => ['position' => 'President',      'name' => 'Ana Marquez'],
            'STU5cMhzm8zbGQD' => ['position' => 'Vice President', 'name' => 'Cedric Quispe'],
            'STUB28sfHCuXKX6' => ['position' => 'Vice President', 'name' => 'Honey Mae Malang'],
            'STUI3qa1me1FKU2' => ['position' => 'Senator',        'name' => 'Rosa Yu'],
            'STUMB3Qrc8c7o9H' => ['position' => 'Senator',        'name' => 'Luis Chua'],
            'STUNMgzR1sHq35n' => ['position' => 'Senator',        'name' => 'Juan Yu'],
            'STUSzkEKuMytQej' => ['position' => 'Senator',        'name' => 'Jasmine Quispe'],
            'STUW9VKZhV9G6qG' => ['position' => 'Senator',        'name' => 'Jasmine Go'],
            'STUY2PbDXtwrebs' => ['position' => 'Senator',        'name' => 'Juan Tamad'],
            'STUarBPW7uoLM84' => ['position' => 'Senator',        'name' => 'Jungkook Jung'],
            'STUbl5p9RI4gM1n' => ['position' => 'Senator',        'name' => 'Jose Perolino Jr.'],
            'STUy7Ym2ieT0Npo' => ['position' => 'Senator',        'name' => 'Joshua Cortes'],
            'STUyTtLo04eMvp1' => ['position' => 'Senator',        'name' => 'Carmen Gonzales'],
            'STUvSHtyJ4XGqku' => ['position' => 'Senator',        'name' => 'Jose Perolino Jr. II'],
            'STUKduSLdkaI44y' => ['position' => 'Senator',        'name' => 'Shania Eshi Picardal'],
        ];

        $candRef = $this->db->getReference('candidates');
        $now     = Carbon::now()->toIso8601String();
        $count   = 0;

        foreach ($assignments as $userId => $info) {
            $candRef->push([
                'user_id'     => $userId,
                'full_name'   => $info['name'],
                'position'    => $info['position'],
                'position_id' => $info['position'],
                'election_id' => $electionId,
                'status'      => 'approved',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $this->info("  Added [{$info['position']}]: {$info['name']}");
            $count++;
        }

        $this->newLine();
        $this->info("✓ {$count} candidates seeded successfully.");
    }
}
