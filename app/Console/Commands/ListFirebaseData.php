<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class ListFirebaseData extends Command
{
    protected $signature = 'firebase:list {collection?}';
    protected $description = 'List Firebase data to find the correct candidate identifier';

    public function handle(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $db = $factory->createDatabase();
        $collection = $this->argument('collection') ?? 'candidates';

        $this->info("Listing {$collection} collection:");
        $this->newLine();

        $ref = $db->getReference($collection);
        $snapshot = $ref->getSnapshot();
        
        if ($snapshot->exists()) {
            $data = $snapshot->getValue();
            
            if (empty($data)) {
                $this->line("Collection is empty");
                return;
            }
            
            foreach ($data as $id => $item) {
                $this->line("ID: {$id}");
                
                if (is_array($item)) {
                    foreach ($item as $key => $value) {
                        if (is_string($value) || is_numeric($value)) {
                            $this->line("  {$key}: {$value}");
                        }
                    }
                } else {
                    $this->line("  Value: {$item}");
                }
                
                $this->newLine();
                
                // Look for candidates with "cortes" or "james" or "michael" in their data
                if ($collection === 'candidates' && is_array($item)) {
                    $searchTerms = ['cortes', 'james', 'michael'];
                    $itemString = strtolower(json_encode($item));
                    
                    foreach ($searchTerms as $term) {
                        if (strpos($itemString, $term) !== false) {
                            $this->info("🎯 FOUND MATCH for '{$term}' in candidate: {$id}");
                            break;
                        }
                    }
                }
            }
        } else {
            $this->line("Collection '{$collection}' does not exist or is empty");
        }
    }
}