<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Domain\User\UserRepository;
use Kreait\Firebase\Contract\Database;

class ComelecDashboarController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private Database $db
    ) {}

    public function index(): View
    {
        // Real-time voter turnout
        $turnout = $this->userRepository->realtimeVoterTurnout();

        // Per year level turnout
        $yearLevelData = $this->userRepository->voterTurnoutByYearLevel();

        // Active election info
        $electionSnap = $this->db->getReference('elections')
            ->orderByChild('status')->equalTo('active')->getSnapshot();

        $electionName   = null;
        $electionStatus = 'No Active Election';

        if ($electionSnap->exists() && $electionSnap->getValue()) {
            $election       = array_values($electionSnap->getValue())[0];
            $electionName   = $election['election_name'] ?? 'Active Election';
            $electionStatus = 'Active';
        }

        // Candidate count for active election
        $candidateCount = 0;
        $candSnap = $this->db->getReference('candidates')->getSnapshot();
        if ($candSnap->exists() && $candSnap->getValue() && $electionSnap->exists() && $electionSnap->getValue()) {
            $activeId = array_key_first($electionSnap->getValue());
            foreach ($candSnap->getValue() as $c) {
                if (($c['election_id'] ?? '') === $activeId && ($c['status'] ?? '') === 'approved') {
                    $candidateCount++;
                }
            }
        }

        return view('comelecdashboard', [
            'turnout'        => $turnout,
            'yearLevelData'  => $yearLevelData,
            'electionName'   => $electionName,
            'electionStatus' => $electionStatus,
            'candidateCount' => $candidateCount,
        ]);
    }
}
