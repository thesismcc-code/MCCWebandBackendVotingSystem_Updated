<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CheckElectionSchedule extends Command
{
    protected $signature   = 'election:check-schedule';
    protected $description = 'Auto-start or auto-close elections based on their schedule';

    public function __construct(private Database $db) {
        parent::__construct();
    }

    public function handle(): void
    {
        $now = Carbon::now();

        $allSnap = $this->db->getReference('elections')->getSnapshot();
        if (!$allSnap->exists() || !$allSnap->getValue()) {
            $this->info('No elections found.');
            return;
        }

        foreach ($allSnap->getValue() as $id => $election) {
            $status      = $election['status']       ?? 'upcoming';
            $dateFrom    = $election['date_from']    ?? null;
            $dateTo      = $election['date_to']      ?? null;
            $openTime    = $election['opening_time'] ?? null;
            $closeTime   = $election['closing_time'] ?? null;

            if (!$dateFrom || !$dateTo || !$openTime || !$closeTime) continue;

            // Build full datetime objects
            $startDt = Carbon::parse($dateFrom . ' ' . $openTime);
            $endDt   = Carbon::parse($dateTo   . ' ' . $closeTime);

            // Should be active?
            if ($now->between($startDt, $endDt) && $status !== 'active') {
                $this->db->getReference('elections/' . $id)->update([
                    'status'     => 'active',
                    'updated_at' => $now->toDateTimeLocalString(),
                ]);
                $this->clearCaches();
                $this->info("Election [{$election['election_name']}] started automatically.");
            }

            // Should be closed?
            if ($now->greaterThan($endDt) && $status === 'active') {
                $this->db->getReference('elections/' . $id)->update([
                    'status'     => 'closed',
                    'updated_at' => $now->toDateTimeLocalString(),
                ]);
                $this->clearCaches();
                $this->info("Election [{$election['election_name']}] closed automatically.");
            }
        }

        $this->info('Schedule check complete.');
    }

    private function clearCaches(): void
    {
        Cache::forget('active_election_ids');
        Cache::forget('active_election_ids_candidates');
        Cache::forget('live_vote_cast');
        Cache::forget('live_candidate_result');
        Cache::forget('all_users_raw');
    }
}
