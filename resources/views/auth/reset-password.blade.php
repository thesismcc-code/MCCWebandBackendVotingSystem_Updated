<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — MCC Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f6f0; }
        .field {
            width: 100%; background: #f8faf7; border: 1.5px solid #d1ddd1;
            border-radius: 12px; padding: 0.82rem 3rem 0.82rem 2.85rem;
            font-size: 0.9rem; font-family: inherit; color: #1a3a1a; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .field::placeholder { color: #9cb89c; }
        .field:focus { border-color: #1a5c38; box-shadow: 0 0 0 3px rgba(26,92,56,0.12); background: #fff; }
        .btn-green {
            width: 100%; background: linear-gradient(135deg, #1a5c38 0%, #2d7a52 100%);
            color: #fff; border: none; border-radius: 12px; padding: 0.9rem;
            font-size: 0.9rem; font-weight: 700; font-family: inherit; cursor: pointer;
            letter-spacing: 0.03em; transition: transform .18s, box-shadow .18s;
        }
        .btn-green:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(26,92,56,0.3); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="fixed inset-0 flex items-center justify-center pointer-events-none select-none z-0">
        <img src="{{ asset('icons/logo_white_bg.png') }}" alt="" class="w-[60%] max-w-[500px] opacity-[0.04] object-contain">
    </div>

    <div class="relative z-10 w-full max-w-[420px]">
        <div class="bg-white rounded-[24px] shadow-xl border border-[#e8ede3] px-9 py-10" x-data="{ showP: false, showC: false }">

            <div class="flex flex-col items-center mb-7">
                <div class="w-[64px] h-[64px] rounded-[18px] flex items-center justify-center mb-4"
                     style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 8px 24px rgba(26,92,56,0.3);">
                    <svg width="28" height="28" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-[#1a3a1a] font-extrabold text-[1.18rem] tracking-tight">Set New Password</h2>
                <p class="text-[#6b9e6b] text-[0.78rem] font-medium mt-1 text-center">Choose a strong password for your account</p>
            </div>

            @if(session('error'))
                <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-[0.82rem] font-semibold rounded-xl px-4 py-3 mb-5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                {{-- New Password --}}
                <div class="mb-4">
                    <label class="block text-[0.72rem] font-bold text-[#4a6a4a] uppercase tracking-wider mb-1.5">New Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9cb89c] pointer-events-none flex">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input :type="showP ? 'text' : 'password'" name="password"
                               placeholder="Min. 8 characters" class="field" required>
                        <button type="button" @click="showP = !showP"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9cb89c] hover:text-[#4a6a4a] transition-colors">
                            <svg x-show="!showP" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                            <svg x-show="showP" x-cloak width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-6">
                    <label class="block text-[0.72rem] font-bold text-[#4a6a4a] uppercase tracking-wider mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9cb89c] pointer-events-none flex">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <input :type="showC ? 'text' : 'password'" name="password_confirmation"
                               placeholder="Repeat your password" class="field" required>
                        <button type="button" @click="showC = !showC"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9cb89c] hover:text-[#4a6a4a] transition-colors">
                            <svg x-show="!showC" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                            <svg x-show="showC" x-cloak width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-green">Reset Password</button>
            </form>

            <div class="mt-5 text-center">
                <a href="{{ route('login') }}" class="text-[0.8rem] font-semibold text-[#1a5c38] hover:underline inline-flex items-center gap-1.5">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Login
                </a>
            </div>
        </div>
        <p class="text-center text-[0.68rem] text-[#9cb89c] mt-5 font-medium tracking-wide">© {{ date('Y') }} Mandaue City College. All rights reserved.</p>
    </div>

</body>
</html>
