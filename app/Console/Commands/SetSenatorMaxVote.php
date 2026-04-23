<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class SetSenatorMaxVote extends Command
{
    protected $signature   = 'positions:setup';
    protected $description = 'Ensure President=1, Vice President=1, Senator=9 positions exist in Firebase';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        $posRef = $this->db->getReference('positions');
        $snap   = $posRef->getSnapshot();
        $now    = now()->toIso8601String();

        // Map existing positions by name (lowercase)
        $existing = [];
        if ($snap->exists() && $snap->getValue()) {
            foreach ($snap->getValue() as $id => $p) {
                $name = strtolower($p['position_name'] ?? $p['name'] ?? '');
                $existing[$name] = ['id' => $id, 'data' => $p];
            }
        }

        $desired = [
            'President'      => 1,
            'Vice President' => 1,
            'Senator'        => 9,
        ];

        foreach ($desired as $name => $maxVote) {
            $key = strtolower($name);
            if (isset($existing[$key])) {
                $id      = $existing[$key]['id'];
                $current = (int) ($existing[$key]['data']['max_vote'] ?? 0);
                if ($current !== $maxVote || ($existing[$key]['data']['position_name'] ?? '') !== $name) {
                    $posRef->getChild($id)->update([
                        'position_name' => $name,
                        'max_vote'      => $maxVote,
                        'updated_at'    => $now,
                    ]);
                    $this->info("  Updated: {$name} → max_vote = {$maxVote}");
                } else {
                    $this->line("  OK: {$name} (max_vote = {$maxVote})");
                }
            } else {
                $posRef->push([
                    'position_name' => $name,
                    'max_vote'      => $maxVote,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
                $this->info("  Created: {$name} → max_vote = {$maxVote}");
            }
        }

        $this->newLine();
        $this->info('✓ Positions configured: President=1, Vice President=1, Senator=9');
    }
}
