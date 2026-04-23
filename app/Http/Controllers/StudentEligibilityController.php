<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Domain\User\UserRepository;

class StudentEligibilityController extends Controller
{
    public function __construct(private UserRepository $userRepository) {}

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $course = $request->input('course');
        $status = $request->input('status'); // 'eligible', 'not_eligible', or ''

        $data = $this->userRepository->getStudentsEligibility(10, $search, $course, $status);

        return view('studeneligibility', [
            'total'       => $data['total'],
            'eligible'    => $data['eligible'],
            'notEligible' => $data['notEligible'],
            'students'    => $data['paginator'],
            'search'      => $search,
            'course'      => $course,
        ]);
    }
}
