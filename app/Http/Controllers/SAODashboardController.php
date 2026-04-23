<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Contract\Database;
use App\Domain\User\UserRepository;

class SAODashboardController extends Controller
{
    public function __construct(
        private Database $db,
        private UserRepository $userRepository
    ) {}

    public function index(): View
    {
        $turnout  = $this->userRepository->realtimeVoterTurnout();
        $byYear   = $this->userRepository->voterTurnoutByYearLevel();

        $electionsSnap = $this->db->getReference('elections')->orderByChild('status')->equalTo('active')->getSnapshot();
        $electionName  = 'No Active Election';
        $electionStatus = 'inactive';
        if ($electionsSnap->exists() && $electionsSnap->getValue()) {
            $e = array_values($electionsSnap->getValue())[0];
            $electionName   = $e['election_name'] ?? $electionName;
            $electionStatus = $e['status']        ?? $electionStatus;
        }

        return view('saodashboard', compact('turnout', 'byYear', 'electionName', 'electionStatus'));
    }
}
