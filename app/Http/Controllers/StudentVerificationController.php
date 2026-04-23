<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Domain\User\UserRepository;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class StudentVerificationController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private Database $db
    ) {}

    public function index(): View|RedirectResponse
    {
        if (!Session::has('auth_user') || Session::get('auth_user.role') !== 'student') {
            return redirect('/students');
        }

        $userId = Session::get('auth_user.id');
        $email  = Session::get('auth_user.email');

        $cacheKey = 'otp_' . $userId;
        if (!Cache::has($cacheKey)) {
            $this->sendOtp($userId, $email);
        }

        return view('student.verify', [
            'email' => $this->maskEmail($email),
        ]);
    }

    public function sendOtpAction(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!Session::has('auth_user') || Session::get('auth_user.role') !== 'student') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $userId = Session::get('auth_user.id');
        $email  = Session::get('auth_user.email');

        $this->sendOtp($userId, $email);

        return response()->json(['success' => true, 'message' => 'OTP resent successfully.']);
    }

    public function verifyOtp(Request $request): View|RedirectResponse
    {
        if (!Session::has('auth_user') || Session::get('auth_user.role') !== 'student') {
            return redirect('/students');
        }

        $request->validate([
            'otp' => 'required|string|size:4',
        ]);

        $userId   = Session::get('auth_user.id');
        $email    = Session::get('auth_user.email');
        $cacheKey = 'otp_' . $userId;
        $stored   = Cache::get($cacheKey);

        Log::info('OTP verify attempt', [
            'session_user_id' => $userId,
            'email'           => $email,
            'otp_submitted'   => $request->input('otp'),
            'otp_stored'      => $stored,
        ]);

        if (!$stored || $stored !== $request->input('otp')) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // Resolve the real Firebase key — look up by email to be 100% sure
        $firebaseKey = $this->resolveFirebaseKey($userId, $email);

        if (!$firebaseKey) {
            Log::error('OTP verify: could not resolve Firebase key', [
                'session_user_id' => $userId,
                'email'           => $email,
            ]);
            return back()->withErrors(['otp' => 'Account not found. Please contact support.']);
        }

        $now = Carbon::now()->toDateTimeLocalString();

        // Write email_verified_at to Firebase
        $this->db->getReference('users/' . $firebaseKey)->update([
            'email_verified_at' => $now,
        ]);

        // Verify the write actually happened
        $written = $this->db->getReference('users/' . $firebaseKey . '/email_verified_at')->getValue();

        Log::info('OTP verify: Firebase write result', [
            'firebase_key'      => $firebaseKey,
            'email_verified_at' => $written,
        ]);

        // Clear caches
        Cache::forget($cacheKey);
        Cache::forget('all_users_raw');

        // Update session
        Session::put('auth_user.id', $firebaseKey); // ensure session has correct key
        Session::put('auth_user.email_verified_at', $now);

        return redirect()->route('view.student-dashboard')
            ->with('success', 'Email verified successfully. You are now eligible to vote!');
    }

    /**
     * Resolve the correct Firebase key.
     * First tries the session ID directly, then falls back to email lookup.
     */
    private function resolveFirebaseKey(string $sessionId, string $email): ?string
    {
        // Try session ID directly first
        $direct = $this->db->getReference('users/' . $sessionId)->getValue();
        if ($direct && isset($direct['email'])) {
            Log::info('resolveFirebaseKey: matched by session ID', ['key' => $sessionId]);
            return $sessionId;
        }

        // Fallback: search by email
        $snapshot = $this->db->getReference('users')
            ->orderByChild('email')
            ->equalTo($email)
            ->getSnapshot();

        if ($snapshot->exists() && $snapshot->getValue()) {
            $key = array_key_first($snapshot->getValue());
            Log::info('resolveFirebaseKey: matched by email lookup', ['key' => $key, 'email' => $email]);
            return $key;
        }

        return null;
    }

    private function sendOtp(string $userId, string $email): void
    {
        $otp      = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $cacheKey = 'otp_' . $userId;

        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        Log::info('OTP sent', ['user_id' => $userId, 'email' => $email, 'otp' => $otp]);

        Mail::send([], [], function ($message) use ($email, $otp) {
            $message->to($email)
                ->subject('Your MCC Voting System Verification Code')
                ->html($this->buildOtpEmail($otp));
        });
    }

    private function buildOtpEmail(string $otp): string
    {
        return <<<HTML
        <div style="font-family: Inter, Arial, sans-serif; max-width: 480px; margin: 0 auto; padding: 32px; background: #f9fafb; border-radius: 12px;">
            <div style="text-align: center; margin-bottom: 24px;">
                <h1 style="color: #152B66; font-size: 22px; font-weight: 700; margin: 0;">MCC Digital Voting System</h1>
                <p style="color: #6b7280; font-size: 14px; margin-top: 6px;">Email Verification</p>
            </div>
            <div style="background: #ffffff; border-radius: 10px; padding: 28px; text-align: center; border: 1px solid #e5e7eb;">
                <p style="color: #374151; font-size: 15px; margin-bottom: 20px;">Your one-time verification code is:</p>
                <div style="letter-spacing: 16px; font-size: 42px; font-weight: 800; color: #152B66; margin: 0 auto 20px;">{$otp}</div>
                <p style="color: #9ca3af; font-size: 13px;">This code expires in <strong>10 minutes</strong>.</p>
            </div>
            <p style="color: #9ca3af; font-size: 12px; text-align: center; margin-top: 20px;">If you did not request this, please ignore this email.</p>
        </div>
        HTML;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, min(3, strlen($local)));
        $masked  = $visible . str_repeat('*', max(0, strlen($local) - 3));
        return $masked . '@' . $domain;
    }
}
