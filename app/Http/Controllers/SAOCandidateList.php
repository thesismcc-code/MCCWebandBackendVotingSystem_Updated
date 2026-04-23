<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Contract\Database;

class SAOCandidateList extends Controller
{
    public function __construct(private Database $db) {}

    public function index(): View
    {
        $electionsSnap    = $this->db->getReference('elections')->orderByChild('status')->equalTo('active')->getSnapshot();
        $activeElectionId = null;
        if ($electionsSnap->exists() && $electionsSnap->getValue()) {
            $activeElectionId = array_key_first($electionsSnap->getValue());
        }

        $candSnap  = $this->db->getReference('candidates')->getSnapshot();
        $usersSnap = $this->db->getReference('users')->getSnapshot();
        $users     = $usersSnap->exists() ? ($usersSnap->getValue() ?? []) : [];

        $byPosition = [];
        if ($candSnap->exists() && $candSnap->getValue()) {
            foreach ($candSnap->getValue() as $cid => $c) {
                if (($c['election_id'] ?? '') !== $activeElectionId || ($c['status'] ?? '') !== 'approved') continue;
                $pos  = $c['position_id'] ?? $c['position'] ?? 'Unknown';
                $uid  = $c['user_id']  ?? $cid;
                $u    = $users[$uid]   ?? [];

                if (!empty($c['full_name'])) {
                    $name = $c['full_name'];
                } else {
                    $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Unknown';
                }

                // Resolve photo
                $photo = $c['image'] ?? $u['profile_photo'] ?? $u['photo_url'] ?? null;
                if ($photo && !str_starts_with($photo, 'http')) {
                    $photo = asset($photo);
                }

                $byPosition[$pos][] = [
                    'name'       => $name,
                    'party_list' => $c['party_list_id'] ?? $c['party_list'] ?? null,
                    'course'     => $u['course'] ?? null,
                    'year_level' => $u['year_level'] ?? null,
                    'photo'      => $photo,
                ];
            }
        }

        // Sort positions by standard order
        $order = ['President','Vice President','Secretary','Treasurer','Auditor','PRO'];
        uksort($byPosition, function($a, $b) use ($order) {
            $ia = array_search($a, $order); $ib = array_search($b, $order);
            return ($ia === false ? 999 : $ia) <=> ($ib === false ? 999 : $ib);
        });

        return view('saocandidatelist', compact('byPosition'));
    }
}
