<?php

namespace App\Eloquent\Vote;

use App\Domain\Votes\Votes;
use App\Domain\Votes\VotesRepository;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Database\Reference;

class EloquentVoteRepository implements VotesRepository
{
    private Reference $db;
    private Reference $votesDb;
    private Reference $electionsDb;
    private Reference $candidatesDb;
    private Reference $usersDb;

    private const POSITION_ORDER = [
        'President',
        'Vice President',
        'Secretary',
        'Treasurer',
        'Auditor',
        'PRO',
    ];

    public function __construct(Database $db)
    {
        $this->db           = $db->getReference('users');
        $this->votesDb      = $db->getReference('votes');
        $this->electionsDb  = $db->getReference('elections');
        $this->candidatesDb = $db->getReference('candidates');
        $this->usersDb      = $db->getReference('users');
    }

    public function getVotes(int $id): ?Votes
    {
        $snapshot = $this->votesDb->orderByChild('voter_id')->equalTo((string) $id)->getSnapshot();
        if (!$snapshot->exists() || !$snapshot->getValue()) return null;
        $data = array_values($snapshot->getValue())[0];
        return new Votes(
            $data['id'] ?? null,
            $data['voter_id'] ?? '',
            $data['candidate_id'] ?? '',
            $data['election_id'] ?? '',
            $data['position'] ?? '',
            $data['created_at'] ?? '',
            $data['updated_at'] ?? $data['created_at'] ?? '',
        );
    }

    public function getVotesCount(int $id): int
    {
        $snapshot = $this->votesDb->orderByChild('voter_id')->equalTo((string) $id)->getSnapshot();
        if (!$snapshot->exists() || !$snapshot->getValue()) return 0;
        return count($snapshot->getValue());
    }

    public function addVote(Votes $vote): ?Votes
    {
        $data = $vote->toArray();
        $data['created_at'] = $data['created_at'] ?: now()->toIso8601String();
        $data['updated_at'] = $data['updated_at'] ?: $data['created_at'];
        $ref = $this->votesDb->push($data);
        return new Votes(
            $ref->getKey(),
            $vote->getVotersId(),
            $vote->getCandidateId(),
            $vote->getElectionId(),
            $vote->getPosition(),
            $data['created_at'],
            $data['updated_at'],
        );
    }

    public function liveVoteCast(): int
    {
        return cache()->remember('live_vote_cast', now()->addSeconds(30), function () {
            $electionSnapshot = $this->electionsDb
                ->orderByChild('status')
                ->equalTo('active')
                ->getSnapshot();

            if (!$electionSnapshot->exists() || $electionSnapshot->getValue() === null) {
                return 0;
            }

            $activeElectionId = array_key_first($electionSnapshot->getValue());

            if ($activeElectionId === null) {
                return 0;
            }

            $votesSnapshot = $this->votesDb
                ->orderByChild('election_id')
                ->equalTo($activeElectionId)
                ->getSnapshot();

            if (!$votesSnapshot->exists() || $votesSnapshot->getValue() === null) {
                return 0;
            }

            $uniqueVoterIds = [];
            foreach ($votesSnapshot->getValue() as $vote) {
                if (isset($vote['voter_id'])) {
                    $uniqueVoterIds[$vote['voter_id']] = true;
                }
            }

            return count($uniqueVoterIds);
        });
    }

    public function liveCandidateResult(): array
    {
        return cache()->remember('live_candidate_result', now()->addSeconds(30), function () {
            $electionSnapshot = $this->electionsDb
                ->orderByChild('status')
                ->equalTo('active')
                ->getSnapshot();

            if (!$electionSnapshot->exists() || $electionSnapshot->getValue() === null) {
                return [];
            }

            $activeElections  = $electionSnapshot->getValue();
            $activeElectionId = array_key_first($activeElections);

            if ($activeElectionId === null) {
                return [];
            }

            $activeElection = $activeElections[$activeElectionId];

            if (($activeElection['status'] ?? '') !== 'active') {
                return [];
            }

            // ── Step 1: Build approved candidate map for this election ────────────
            $candidatesSnapshot = $this->candidatesDb->getSnapshot();

            $candidateMap = [];
            if ($candidatesSnapshot->exists() && $candidatesSnapshot->getValue() !== null) {
                foreach ($candidatesSnapshot->getValue() as $candId => $candData) {
                    if (
                        isset($candData['election_id'], $candData['status']) &&
                        $candData['election_id'] === $activeElectionId &&
                        $candData['status']      === 'approved'
                    ) {
                        $candidateMap[$candId] = $candData;
                    }
                }
            }

            if (empty($candidateMap)) {
                return [];
            }

            // ── Step 2: Fetch votes for this election ─────────────────────────────
            $votesSnapshot = $this->votesDb
                ->orderByChild('election_id')
                ->equalTo($activeElectionId)
                ->getSnapshot();

            if (!$votesSnapshot->exists() || $votesSnapshot->getValue() === null) {
                return $this->buildZeroResults($candidateMap);
            }

            // ── Step 3: Tally votes per position per candidate ────────────────────
            $tally = [];
            foreach ($votesSnapshot->getValue() as $vote) {
                $position    = $vote['position']     ?? null;
                $candidateId = $vote['candidate_id'] ?? null;

                if (!$position || !$candidateId) continue;

                // Skip votes for candidates not in the approved candidate map
                if (!isset($candidateMap[$candidateId])) continue;

                $tally[$position][$candidateId] = ($tally[$position][$candidateId] ?? 0) + 1;
            }

            // ── Step 4: Resolve full name from /users (cached) ───────────────────
            $nameCache   = [];
            $resolveName = function (string $userId) use (&$nameCache): string {
                if (isset($nameCache[$userId])) {
                    return $nameCache[$userId];
                }
                $userData = $this->usersDb->getChild($userId)->getValue();
                if (!$userData) {
                    return $nameCache[$userId] = 'Unknown';
                }
                return $nameCache[$userId] = trim(
                    ($userData['first_name']  ?? '') . ' ' .
                    ($userData['middle_name'] ?? '') . ' ' .
                    ($userData['last_name']   ?? '')
                ) ?: 'Unknown';
            };

            // ── Step 5: Build results array ───────────────────────────────────────
            $results = [];

            foreach ($tally as $position => $candidateVotes) {
                $totalVotesInPosition = array_sum($candidateVotes);
                $positionResults      = [];

                foreach ($candidateVotes as $candidateId => $voteCount) {
                    $candData = $candidateMap[$candidateId];
                    $userId   = $candData['user_id'] ?? $candidateId;

                    $positionResults[] = [
                        'candidate_id'  => $candidateId,
                        'name'          => $resolveName($userId),
                        'party_list_id' => $candData['party_list_id'] ?? null,
                        'votes'         => $voteCount,
                        'percentage'    => $totalVotesInPosition > 0
                            ? round(($voteCount / $totalVotesInPosition) * 100, 2)
                            : 0.0,
                    ];
                }

                // Sort candidates within each position by votes descending
                usort($positionResults, fn($a, $b) => $b['votes'] <=> $a['votes']);

                $results[$position] = $positionResults;
            }

            // ── Step 6: Sort positions by defined order ───────────────────────────
            uksort($results, function (string $a, string $b): int {
                $orderA = array_search($a, self::POSITION_ORDER);
                $orderB = array_search($b, self::POSITION_ORDER);

                $orderA = $orderA === false ? PHP_INT_MAX : $orderA;
                $orderB = $orderB === false ? PHP_INT_MAX : $orderB;

                return $orderA <=> $orderB;
            });

            return $results;
        });
    }

    private function buildZeroResults(array $candidateMap): array
    {
        $nameCache   = [];
        $resolveName = function (string $userId) use (&$nameCache): string {
            if (isset($nameCache[$userId])) {
                return $nameCache[$userId];
            }
            $userData = $this->usersDb->getChild($userId)->getValue();
            if (!$userData) {
                return $nameCache[$userId] = 'Unknown';
            }
            return $nameCache[$userId] = trim(
                ($userData['first_name']  ?? '') . ' ' .
                ($userData['middle_name'] ?? '') . ' ' .
                ($userData['last_name']   ?? '')
            ) ?: 'Unknown';
        };

        $results = [];
        foreach ($candidateMap as $candidateId => $candData) {
            $position = $candData['position'] ?? 'Unknown';
            $userId   = $candData['user_id']  ?? $candidateId;

            $results[$position][] = [
                'candidate_id'  => $candidateId,
                'name'          => $resolveName($userId),
                'party_list_id' => $candData['party_list_id'] ?? null,
                'votes'         => 0,
                'percentage'    => 0.0,
            ];
        }

        // Sort positions by defined order
        uksort($results, function (string $a, string $b): int {
            $orderA = array_search($a, self::POSITION_ORDER);
            $orderB = array_search($b, self::POSITION_ORDER);

            $orderA = $orderA === false ? PHP_INT_MAX : $orderA;
            $orderB = $orderB === false ? PHP_INT_MAX : $orderB;

            return $orderA <=> $orderB;
        });

        return $results;
    }
}
