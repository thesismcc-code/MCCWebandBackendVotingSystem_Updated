<?php

namespace App\Eloquent\Candidates;

use App\Domain\Candidates\Candidates;
use App\Domain\Candidates\CandidatesRepository;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Database\Reference;

class EloquentCandidateRepository implements CandidatesRepository
{
    private Reference $db;
    private Reference $electionsDb;
    private const COLLECTIONS            = 'candidates';
    private const ELECTIONS_COLLECTIONS  = 'elections';

    public function __construct(Database $db)
    {
        $this->db          = $db->getReference(self::COLLECTIONS);
        $this->electionsDb = $db->getReference(self::ELECTIONS_COLLECTIONS);
    }

    /**
     * Cache active election IDs to avoid repeated queries.
     * Returns array of IDs.
     */
    private function getActiveElectionIds(): array
    {
        return cache()->remember('active_election_ids_candidates', now()->addMinutes(5), function () {
            $snapshot = $this->electionsDb
                ->orderByChild('status')
                ->equalTo('active')
                ->getSnapshot();

            $data = $snapshot->exists() && $snapshot->getValue() !== null
                ? $snapshot->getValue()
                : null;

            // Ensure we return an array, even if empty
            return array_keys($data ?? []);
        });
    }

    private function toCandidate(string $id, array $data): Candidates
    {
        return new Candidates(
            id:             $id,
            party_list_id:  $data['party_list_id']  ?? null,
            election_id:    $data['election_id']     ?? null,
            position_id:       $data['position_id']        ?? null,
            manifesto:      $data['manifesto']       ?? null,
            status:         $data['status']          ?? null,
            created_at:     $data['created_at']      ?? null,
            updated_at:     $data['updated_at']      ?? null,
        );
    }

    public function getCandidates(): array
    {
        $snapshot = $this->db->getSnapshot();
        $data = $snapshot->exists() && $snapshot->getValue() !== null
            ? $snapshot->getValue()
            : [];

        return array_map([$this, 'toCandidate'], array_keys($data));
    }

    /**
     * Get running candidates (filtered by active election)
     */
    public function getRunnCandidates(): array
    {
        return cache()->remember('running_candidates', now()->addMinutes(5), function () {
            $activeElectionIds = $this->getActiveElectionIds();

            if (empty($activeElectionIds)) {
                return [];
            }

            $snapshot = $this->db->getSnapshot();
            $candidates = [];

            foreach ($snapshot->getValue() ?? [] as $key => $data) {
                // Check if data exists before accessing keys
                if (
                    !empty($data['election_id']) &&
                    !empty($data['status']) &&
                    in_array($data['election_id'], $activeElectionIds, true) &&
                    $data['status'] === 'approved'
                ) {
                    $candidates[] = $this->toCandidate((string) $key, $data);
                }
            }

            return $candidates;
        });
    }

    public function getRunnCandidatesCount(): int
    {
        return cache()->remember('running_candidates_count', now()->addMinutes(5), function () {
            return count($this->getRunnCandidates());
        });
    }
}
