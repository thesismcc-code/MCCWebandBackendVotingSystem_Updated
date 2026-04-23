<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — MCC Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f6f0; }
        .field {
            width: 100%; background: #f8faf7; border: 1.5px solid #d1ddd1;
            border-radius: 12px; padding: 0.82rem 1rem 0.82rem 2.85rem;
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
        .btn-green:active { transform: translateY(0); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    {{-- Background watermark --}}
    <div class="fixed inset-0 flex items-center justify-center pointer-events-none select-none z-0">
        <img src="{{ asset('icons/logo_white_bg.png') }}" alt="" class="w-[60%] max-w-[500px] opacity-[0.04] object-contain">
    </div>

    <div class="relative z-10 w-full max-w-[420px]">
        <div class="bg-white rounded-[24px] shadow-xl border border-[#e8ede3] px-9 py-10">

            {{-- Header --}}
            <div class="flex flex-col items-center mb-7">
                <div class="w-[64px] h-[64px] rounded-[18px] flex items-center justify-center mb-4"
                     style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 8px 24px rgba(26,92,56,0.3);">
                    <svg width="28" height="28" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="text-[#1a3a1a] font-extrabold text-[1.18rem] tracking-tight">Forgot Password?</h2>
                <p class="text-[#6b9e6b] text-[0.78rem] font-medium mt-1 text-center">Enter your email and we'll send you a reset link</p>
            </div>

            {{-- Status / Error --}}
            @if(session('status'))
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-[0.82rem] font-semibold rounded-xl px-4 py-3 mb-5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-[0.82rem] font-semibold rounded-xl px-4 py-3 mb-5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.send') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-[0.72rem] font-bold text-[#4a6a4a] uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9cb89c] pointer-events-none flex">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email" name="email" placeholder="your@email.com"
                               value="{{ old('email') }}" class="field" required autofocus>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-green">
                    Send Reset Link
                </button>
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
