<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class RestoreCandidate extends Command
{
    protected $signature = 'firebase:restore-candidate';
    protected $description = 'Restore cortesjamesmichael candidate to Firebase';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();

        $this->info('Restoring cortesjamesmichael candidate...');
        $this->newLine();

        // Create the candidate data
        $candidateData = [
            'id' => 'cortesjamesmichael',
            'candidate_id' => 'cortesjamesmichael',
            'first_name' => 'James Michael',
            'last_name' => 'Cortes',
            'full_name' => 'James Michael Cortes',
            'position' => 'President', // You can adjust this
            'party_list' => 'Independent', // You can adjust this
            'platform' => 'Leadership and Excellence',
            'is_active' => true,
            'vote_count' => 0,
            'created_at' => now()->toDateTimeLocalString(),
            'updated_at' => now()->toDateTimeLocalString(),
        ];

        // Add the candidate to Firebase
        $candidatesRef = $db->getReference('candidates');
        $candidatesRef->getChild('cortesjamesmichael')->set($candidateData);

        $this->info('✅ Candidate restored successfully!');
        $this->line('Candidate ID: cortesjamesmichael');
        $this->line('Name: James Michael Cortes');
        $this->line('Position: President');
        $this->newLine();

        // Also check current Firebase structure
        $this->info('Current Firebase collections:');
        $rootSnapshot = $db->getReference()->getSnapshot();
        
        if ($rootSnapshot->exists()) {
            foreach ($rootSnapshot->getValue() as $collection => $data) {
                if (is_array($data)) {
                    $count = count($data);
                    $this->line("- {$collection} ({$count} items)");
                } else {
                    $this->line("- {$collection}");
                }
            }
        }
    }
}