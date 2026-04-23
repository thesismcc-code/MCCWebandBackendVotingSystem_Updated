<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Domain\User\UserRepository;

class ForgotPasswordController extends Controller
{
    public function __construct(private UserRepository $userRepository) {}

    // ── Show "Forgot Password" form ──────────────────────────────
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // ── Send reset link ──────────────────────────────────────────
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        $user  = $this->userRepository->findByEmail($email);

        // Always show success to prevent email enumeration
        if (!$user) {
            return back()->with('status', 'If that email exists in our system, a reset link has been sent.');
        }

        $token = Str::random(64);

        // Store token in file cache for 60 minutes
        Cache::put('pwd_reset_' . $token, $email, now()->addMinutes(60));

        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($email));

        Mail::send('auth.emails.reset-password', [
            'resetUrl'  => $resetUrl,
            'firstName' => $user->getFirstName(),
        ], function ($message) use ($email, $user) {
            $message->to($email, $user->getFirstName() . ' ' . $user->getLastName())
                    ->subject('MCC Voting System — Password Reset Request');
        });

        return back()->with('status', 'A password reset link has been sent to your email address.');
    }

    // ── Show reset form ──────────────────────────────────────────
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email || !Cache::has('pwd_reset_' . $token)) {
            return redirect('/')->with('error', 'This password reset link is invalid or has expired.');
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    // ── Handle reset ─────────────────────────────────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
        ]);

        $token = $request->input('token');
        $email = $request->input('email');

        $cached = Cache::get('pwd_reset_' . $token);

        if (!$cached || $cached !== $email) {
            return back()->with('error', 'This password reset link is invalid or has expired.');
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return back()->with('error', 'No account found with that email address.');
        }

        // Build updated user with new hashed password
        $updated = new \App\Domain\User\User(
            $user->getId(),
            $user->getFirstName(),
            $user->getMiddleName(),
            $user->getLastName(),
            $user->getEmail(),
            Hash::make($request->input('password')),
            $user->getRole(),
            $user->getAdminId(),
            $user->getStudentId(),
            $user->getComelecId(),
            $user->getEmailVerifiedAt(),
            $user->getCreatedAt(),
            null,
            $user->getYearLevel(),
            $user->getCourse(),
        );

        $this->userRepository->updateUser($updated);

        Cache::forget('pwd_reset_' . $token);

        return redirect('/')->with('status', 'Your password has been reset successfully. You can now log in.');
    }
}
