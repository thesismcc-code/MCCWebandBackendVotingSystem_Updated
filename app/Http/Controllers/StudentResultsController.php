<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Kreait\Firebase\Contract\Database;

class StudentResultsController extends Controller
{
    public function __construct(private Database $db) {}

    public function index(): View|RedirectResponse
    {
        // Find closed + published election
        $allSnap = $this->db->getReference('elections')->getSnapshot();
        $electionId   = null;
        $electionName = null;
        $publishedAt  = null;

        if ($allSnap->exists() && $allSnap->getValue()) {
            foreach ($allSnap->getValue() as $eid => $edata) {
                if (!empty($edata['results_published'])) {
                    $electionId   = $eid;
                    $electionName = $edata['election_name'] ?? 'Election';
                    $publishedAt  = $edata['published_at'] ?? null;
                    break;
                }
            }
        }

        if (!$electionId) {
            return view('student.results', [
                'results'      => [],
                'electionName' => null,
                'publishedAt'  => null,
            ]);
        }

        // Reuse SAOFinalResult builder
        $builder  = app(SAOFinalResult::class);
        [$results] = $builder->buildResultsForElection($electionId);

        return view('student.results', compact('results', 'electionName', 'publishedAt'));
    }
}
