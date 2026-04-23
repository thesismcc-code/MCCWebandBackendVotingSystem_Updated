<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\User\UserRepository;

class DebugEligibilityPage extends Command
{
    protected $signature   = 'debug:eligibility-page';
    protected $description = 'Simulate what the eligibility page controller returns';

    public function __construct(private UserRepository $userRepository) {
        parent::__construct();
    }

    public function handle(): void
    {
        $data = $this->userRepository->getStudentsEligibility(50);

        $this->info("Total:        {$data['total']}");
        $this->info("Eligible:     {$data['eligible']}");
        $this->info("Not Eligible: {$data['notEligible']}");
        $this->newLine();

        $rows = [];
        foreach ($data['paginator'] as $student) {
            $rows[] = [
                $student->getStudentId(),
                $student->getFirstName() . ' ' . $student->getLastName(),
                $student->getEmail(),
                $student->getEmailVerifiedAt() ? '✓ ELIGIBLE' : '✗ NOT ELIGIBLE',
            ];
        }

        $this->table(['Student ID', 'Name', 'Email', 'Status'], $rows);
    }
}
