<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuickAccessController;
use App\Http\Controllers\ManageAccountController;
use App\Http\Controllers\FingerPrintController;
use App\Http\Controllers\VotingLogsController;
use App\Http\Controllers\SecurityLogsController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\SystemActivityController;
use App\Http\Controllers\ReportAndAnalyticsController;
use App\Http\Controllers\SAODashboardController;
use App\Http\Controllers\SAOCandidateList;
use App\Http\Controllers\SAOVoterParticipationController;
use App\Http\Controllers\SAOFinalResult;
use App\Http\Controllers\ComelecDashboarController;
use App\Http\Controllers\ComelectManageCandidate;
use App\Http\Controllers\StudentEligibilityController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentVerificationController;
use App\Http\Controllers\StudentTutorialController;
use App\Http\Controllers\StudentVoteTutorialController;
use App\Http\Controllers\StudentResultsController;
use App\Http\Controllers\ForgotPasswordController;

// ── Public routes ─────────────────────────────────────────────
Route::get('/',        [AuthController::class, 'index'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post')->middleware('throttle:admin-login');

// ── Forgot / Reset Password ────────────────────────────────────
Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.send')->middleware('throttle:forgot-password');
Route::get('/reset-password',         [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',        [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// CSRF token refresh endpoint
Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::get('/students',         [AuthController::class, 'studentIndex'])->name('view.student');
Route::post('/validate-login',  [AuthController::class, 'studentLogin'])->name('validate-login')->middleware('throttle:student-login');
Route::get('/students-tutorials',            [StudentTutorialController::class, 'index'])->name('view.student-tutorials');
Route::get('/students-how-to-vote',          [StudentVoteTutorialController::class, 'index'])->name('view.students-how-to-vote');

// ── Admin routes ──────────────────────────────────────────────
Route::middleware('auth.session')->group(function () {
    Route::get('/dashboard',                              [DashboardController::class, 'index'])->name('view.dashboard');
    Route::get('/dashboard/refresh',                      [DashboardController::class, 'refresh'])->name('dashboard.refresh');
    Route::get('/quick-access',                           [QuickAccessController::class, 'index'])->name('view.quick-access');
    Route::get('/manage-accounts',                        [ManageAccountController::class, 'index'])->name('view.manage-accounts');
    Route::post('/store-new-accounts',                    [ManageAccountController::class, 'newUser'])->name('store.new-accounts');
    Route::post('/update-account',                        [ManageAccountController::class, 'updateUser'])->name('account.update');
    Route::delete('/delete-account/{id}',                 [ManageAccountController::class, 'deleteUser'])->name('account.delete');
    Route::get('/finger-print',                    [FingerPrintController::class, 'index'])->name('view.finger-print');
    Route::get('/finger-print/status',             [FingerPrintController::class, 'status']);
    Route::post('/finger-print/init',              [FingerPrintController::class, 'init']);
    Route::post('/finger-print/terminate',         [FingerPrintController::class, 'terminate']);
    Route::post('/finger-print/capture',           [FingerPrintController::class, 'capture']);
    Route::post('/finger-print/identify',          [FingerPrintController::class, 'identify']);
    Route::post('/finger-print/register',          [FingerPrintController::class, 'register']);
    Route::post('/finger-print/load-templates',    [FingerPrintController::class, 'loadTemplates']);
    Route::delete('/finger-print/delete/{userId}', [FingerPrintController::class, 'destroy']);
    Route::post('/fingerprint/student/update',     [FingerPrintController::class, 'updateStudent'])->name('fingerprint.student.update');
    Route::post('/fingerprint/student/add',        [FingerPrintController::class, 'addStudent'])->name('fingerprint.student.add');
    Route::get('/voting-logs',                            [VotingLogsController::class, 'index'])->name('view.voting-logs');
    Route::get('/voting-logs/export-pdf',                 [VotingLogsController::class, 'exportPdf'])->name('voting-logs.export-pdf');
    Route::get('/security-logs',                          [SecurityLogsController::class, 'index'])->name('view.security-logs');
    Route::get('/election-control',                       [ElectionController::class, 'index'])->name('view.election-control');
    Route::post('/election-control/general-settings',     [ElectionController::class, 'saveGeneralSettings'])->name('election.save-general');
    Route::post('/election-control/schedule-settings',    [ElectionController::class, 'saveScheduleSettings'])->name('election.save-schedule');
    Route::post('/election-control/update-status',        [ElectionController::class, 'updateElectionStatus'])->name('election.update-status');
    Route::get('/election-control-posistion-setup',       [ElectionController::class, 'indexPosistionSetup'])->name('view.election-control-posistion-setup');
    Route::post('/election-control/position/add',         [ElectionController::class, 'addPosition'])->name('position.add');
    Route::post('/election-control/position/update',      [ElectionController::class, 'updatePosition'])->name('position.update');
    Route::delete('/election-control/position/delete/{id}', [ElectionController::class, 'deletePosition'])->name('position.delete');
    Route::get('/election-control-candidate-list',        [ElectionController::class, 'indexCandidateList'])->name('view.election-control-candidate-list');
    Route::post('/election-control/candidate/add',        [ElectionController::class, 'addCandidate'])->name('candidate.add');
    Route::put('/election-control/candidate/update',      [ElectionController::class, 'updateCandidate'])->name('candidate.update');
    Route::delete('/election-control/candidate/delete/{id}', [ElectionController::class, 'deleteCandidate'])->name('candidate.delete');
    Route::get('/system-activity',                        [SystemActivityController::class, 'index'])->name('view.system-activity');
    Route::get('/reports-and-analytics',                  [ReportAndAnalyticsController::class, 'index'])->name('view.reports-and-analytics');
    Route::get('/reports-and-analytics-end-of-election',  [ReportAndAnalyticsController::class, 'indexEndOfElection'])->name('view.reports-and-analytics-end-of-election');
    Route::get('/reports-and-analytics-end-of-election/export-pdf', [ReportAndAnalyticsController::class, 'exportEndOfElectionPdf'])->name('end-of-election.export-pdf');
    Route::get('/election-history',                        [ElectionController::class, 'electionHistory'])->name('view.election-history');
    Route::get('/export/voter-list-csv',                   [DashboardController::class, 'exportVoterListCsv'])->name('export.voter-list-csv');
    Route::post('/logout',                                [AuthController::class, 'logout'])->name('logout');
});

// ── Comelec routes ──────────────────────────────────────
Route::middleware('auth.session')->group(function () {
    Route::get('/sao-dashboard',              [SAODashboardController::class, 'index'])->name('view.sao-dashboard');
    Route::get('/sao-candidate-list',         [SAOCandidateList::class, 'index'])->name('view.sao-candidate-list');
    Route::get('/sao-voter-participation',    [SAOVoterParticipationController::class, 'index'])->name('view.sao-voter-participation');
    Route::get('/sao-final-results',          [SAOFinalResult::class, 'index'])->name('view.sao-final-results');
    Route::post('/sao-publish-results',       [SAOFinalResult::class, 'publish'])->name('sao.publish-results');
    Route::get('/student-eligibility',        [StudentEligibilityController::class, 'index'])->name('view.student-eligibility');
});


// ── Comelec routes ──────────────────────────────────────
Route::middleware('auth.session')->group(function () {
    Route::get('/comelec-dashboard',          [ComelecDashboarController::class, 'index'])->name('view.comelec-dashboard');
    Route::get('/comelec-manage-candidates',  [ComelectManageCandidate::class, 'index'])->name('view.comelec-manage-candidates');
    Route::post('/comelec-candidates/add',    [ComelectManageCandidate::class, 'addCandidate'])->name('comelec.candidate.add');
    Route::delete('/comelec-candidates/{id}', [ComelectManageCandidate::class, 'deleteCandidate'])->name('comelec.candidate.delete');
    Route::post('/comelec-candidates/status', [ComelectManageCandidate::class, 'updateStatus'])->name('comelec.candidate.status');
});

// ── Student routes ────────────────────────────────────────────
Route::middleware('auth.session')->group(function () {
    Route::post('/student-validate-login', [AuthController::class, 'studentLogin'])->name('student.login')->middleware('throttle:student-login');
    Route::post('/student-logout', [AuthController::class, 'logoutStudent'])->name('student.logout');
    Route::get('/students-dashboard',            [StudentDashboardController::class, 'index'])->name('view.student-dashboard');
    Route::get('/students-profile',              [StudentProfileController::class, 'index'])->name('view.student-profile');
    Route::post('/students-update-profile',      [StudentProfileController::class, 'updateProfile'])->name('update-profile');
    Route::get('/students-verification',         [StudentVerificationController::class, 'index'])->name('view.student-verification');
    Route::post('/students-verify-otp',          [StudentVerificationController::class, 'verifyOtp'])->name('student.verify-otp');
    Route::post('/students-resend-otp',          [StudentVerificationController::class, 'sendOtpAction'])->name('student.resend-otp');
    Route::get('/students-results',              [StudentResultsController::class, 'index'])->name('view.student-results');
});

