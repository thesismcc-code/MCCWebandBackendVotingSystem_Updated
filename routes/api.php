<?php

use App\Http\Controllers\UserTestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ManageAccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FingerPrintController;
use App\Http\Controllers\MobileApiController;

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");

Route::get("/hello-world", function () {
    return response()->json(["message" => "Hello, World!"]);
});

Route::get("/users", [UserTestController::class, "index"]);
Route::post("/users", [UserTestController::class, "store"]);

Route::get("/all-users", [UserController::class, "getAllUsers"]);

Route::post("/new/user", [ManageAccountController::class,"newUserAPI"]);

// ── ZKTeco Fingerprint (web dashboard) ───────────────────────
Route::prefix('fingerprint')->group(function () {
    Route::post('/init',           [FingerPrintController::class, 'init']);
    Route::post('/terminate',      [FingerPrintController::class, 'terminate']);
    Route::get('/status',          [FingerPrintController::class, 'status']);
    Route::post('/capture',        [FingerPrintController::class, 'capture']);
    Route::post('/identify',       [FingerPrintController::class, 'identify']);
    Route::post('/match',          [FingerPrintController::class, 'match']);
    Route::post('/register',       [FingerPrintController::class, 'register']);
    Route::post('/load-templates', [FingerPrintController::class, 'loadTemplates']);
    Route::delete('/user/{userId}',[FingerPrintController::class, 'destroy'])->where('userId', '.*');
});

// ── Mobile App API ────────────────────────────────────────────
Route::prefix('mobile')->group(function () {
    // Public
    Route::post('/auth/login',              [MobileApiController::class, 'login']);
    Route::post('/auth/fingerprint-login',  [MobileApiController::class, 'fingerprintLogin']);
    Route::get('/election/active',          [MobileApiController::class, 'activeElection']);
    Route::get('/candidates',               [MobileApiController::class, 'candidates']);
    Route::get('/positions',                [MobileApiController::class, 'positions']);

    // Fingerprint device
    Route::post('/fingerprint/init',        [FingerPrintController::class, 'init']);
    Route::post('/fingerprint/load-templates', [FingerPrintController::class, 'loadTemplates']);
    Route::post('/fingerprint/capture',     [FingerPrintController::class, 'capture']);
    Route::get('/fingerprint/status',       [FingerPrintController::class, 'status']);

    // Protected (JWT)
    Route::middleware('auth.jwt')->group(function () {
        Route::get('/auth/me',              [MobileApiController::class, 'me']);
        Route::post('/vote',                [MobileApiController::class, 'castVote']);
        Route::get('/voter/status',         [MobileApiController::class, 'voterStatus']);
    });
});

