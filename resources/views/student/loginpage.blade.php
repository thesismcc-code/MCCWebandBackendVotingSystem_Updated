<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login — Fingerprint Voting System</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body, html {
            height: 100%; width: 100%;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        /* ── Layout ── */
        .wrapper { display: flex; height: 100vh; width: 100vw; }

        /* ── Left panel ── */
        .left-panel {
            width: 58%;
            position: relative;
            background: url("{{ asset('images/image_1.png') }}") no-repeat center center;
            background-size: cover;
        }

        .overlay {
            position: absolute; inset: 0;
            background: linear-gradient(160deg, rgba(5,20,60,0.72) 0%, rgba(10,50,30,0.60) 100%);
            z-index: 1;
        }

        /* floating fingerprint watermark */
        .fp-watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            opacity: 0.06;
            width: 420px;
            pointer-events: none;
        }

        .left-content {
            position: absolute;
            bottom: 10%;
            left: 9%;
            right: 9%;
            color: #fff;
            z-index: 3;
        }

        .left-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(8px);
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: #d4f5e2;
            margin-bottom: 1.2rem;
        }

        .left-badge span.dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #4ade80;
            display: inline-block;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(1.4); }
        }

        .left-content h1 {
            font-size: 3.2rem;
            line-height: 1.15;
            font-weight: 800;
            margin-bottom: 0.9rem;
            letter-spacing: -0.02em;
        }

        .left-content h1 span { color: #6ee7b7; }

        .left-content p {
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 2rem;
            color: rgba(255,255,255,0.75);
            line-height: 1.6;
        }

        .left-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }

        .left-actions a { text-decoration: none; }

        .btn-outline-white {
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-weight: 600;
            font-size: 0.88rem;
            border: 1.5px solid rgba(255,255,255,0.35);
            padding: 0.72rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            backdrop-filter: blur(6px);
            font-family: inherit;
        }

        .btn-outline-white:hover {
            background: rgba(255,255,255,0.22);
            border-color: rgba(255,255,255,0.6);
        }

        /* stats strip */
        .left-stats {
            display: flex;
            gap: 2rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .stat-item { display: flex; flex-direction: column; gap: 2px; }
        .stat-item .num { font-size: 1.6rem; font-weight: 800; color: #fff; }
        .stat-item .lbl { font-size: 0.72rem; font-weight: 500; color: rgba(255,255,255,0.55); letter-spacing: 0.05em; text-transform: uppercase; }

        /* ── Right panel ── */
        .right-panel {
            width: 42%;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            position: relative;
        }

        /* subtle grid pattern */
        .right-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(17,39,106,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(17,39,106,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
            position: relative;
            z-index: 1;
        }

        /* card */
        .login-card {
            background: #fff;
            border-radius: 24px;
            padding: 2.5rem 2.2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 20px 50px -10px rgba(17,39,106,0.12);
            border: 1px solid rgba(17,39,106,0.07);
        }

        .brand-section { text-align: center; margin-bottom: 1.8rem; }

        .logo {
            width: 72px; height: 72px;
            border-radius: 18px;
            object-fit: contain;
            margin: 0 auto 0.9rem;
            display: block;
            box-shadow: 0 4px 14px rgba(17,39,106,0.15);
        }

        .portal-title {
            color: #0e2060;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .portal-sub {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
            margin-top: 3px;
        }

        /* fingerprint icon row */
        .fp-icon-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 1.2rem 0 1.6rem;
        }

        .fp-icon-row svg { color: #1a3a8f; }

        .fp-icon-row .fp-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #1a3a8f;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 1.4rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            font-size: 0.75rem;
            font-weight: 600;
            color: #9ca3af;
            white-space: nowrap;
        }

        /* toggle */
        .toggle-container {
            display: flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 1.6rem;
            gap: 4px;
        }

        .tab-btn {
            flex: 1;
            background: transparent;
            border: none;
            border-radius: 9px;
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            color: #6b7280;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: #fff;
            color: #0e2060;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        /* inputs */
        .input-group { margin-bottom: 1rem; text-align: left; }

        .input-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
            letter-spacing: 0.02em;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            display: flex;
        }

        .form-control {
            width: 100%;
            background: #f8fafc;
            border: 1.5px solid #e5e7eb;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            border-radius: 12px;
            font-size: 0.92rem;
            font-family: inherit;
            color: #111827;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control::placeholder { color: #9ca3af; }

        .form-control:focus {
            border-color: #1a3a8f;
            box-shadow: 0 0 0 3px rgba(26,58,143,0.1);
            background: #fff;
        }

        .form-control.pr-icon { padding-right: 3rem; }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-pass:hover { color: #374151; }

        /* submit */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #0e2060 0%, #1a3a8f 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.95rem;
            font-size: 0.95rem;
            font-weight: 700;
            margin-top: 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.02em;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, transparent 60%);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(14,32,96,0.35);
        }

        .btn-submit:active { transform: translateY(0); }

        /* error alert */
        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.83rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* footer note */
        .login-footer {
            text-align: center;
            margin-top: 1.4rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .login-footer a { color: #1a3a8f; font-weight: 600; text-decoration: none; }
        .login-footer a:hover { text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            body { overflow-y: auto; overflow-x: hidden; }
            .wrapper { flex-direction: column; height: auto; }
            .left-panel { width: 100%; min-height: 420px; }
            .left-content { bottom: 7%; text-align: center; left: 5%; right: 5%; }
            .left-actions { justify-content: center; }
            .left-stats { justify-content: center; }
            .right-panel { width: 100%; padding: 2.5rem 1.5rem; }
        }

        @media (max-width: 480px) {
            .left-content h1 { font-size: 2rem; }
            .login-card { padding: 2rem 1.4rem; }
        }
    </style>
</head>

<body>

    <div class="wrapper">

        {{-- ── Left panel ── --}}
        <div class="left-panel">
            <div class="overlay"></div>

            {{-- Fingerprint watermark --}}
            <svg class="fp-watermark" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 5C25.1 5 5 25.1 5 50s20.1 45 45 45 45-20.1 45-45S74.9 5 50 5zm0 6c21.5 0 39 17.5 39 39S71.5 89 50 89 11 71.5 11 50s17.5-39 39-39zm0 8c-17.1 0-31 13.9-31 31s13.9 31 31 31 31-13.9 31-31-13.9-31-31-31zm0 6c13.8 0 25 11.2 25 25S63.8 75 50 75 25 63.8 25 50s11.2-25 25-25zm0 6c-10.5 0-19 8.5-19 19s8.5 19 19 19 19-8.5 19-19-8.5-19-19-19zm0 6c7.2 0 13 5.8 13 13s-5.8 13-13 13-13-5.8-13-13 5.8-13 13-13zm0 6c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7z" fill="white"/>
            </svg>

            <div class="left-content">
                <div class="left-badge">
                    <span class="dot"></span>
                    Biometric Voting System — Active
                </div>
                <h1>Welcome to the<br>Official <span>MCC</span><br>Voting Portal.</h1>
                <p>Your Vote, Your Voice — Secured by Fingerprint Technology.</p>
                <div class="left-actions">
                    <a href="{{ route('view.student-tutorials') }}">
                        <button type="button" class="btn-outline-white">
                            <svg style="display:inline;vertical-align:-3px;margin-right:5px;" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Account Setup
                        </button>
                    </a>
                    <a href="{{ route('view.students-how-to-vote') }}">
                        <button type="button" class="btn-outline-white">
                            <svg style="display:inline;vertical-align:-3px;margin-right:5px;" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            How to Vote?
                        </button>
                    </a>
                </div>
                <div class="left-stats">
                    <div class="stat-item">
                        <span class="num">100%</span>
                        <span class="lbl">Secure</span>
                    </div>
                    <div class="stat-item">
                        <span class="num">1-Vote</span>
                        <span class="lbl">Per Student</span>
                    </div>
                    <div class="stat-item">
                        <span class="num">Live</span>
                        <span class="lbl">Results</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right panel ── --}}
        <div class="right-panel">
            <div class="login-container">
                <div class="login-card">

                    <div class="brand-section">
                        <img class="logo" src="{{ asset('icons/logo_white_bg.png') }}" alt="Mandaue City College Logo">
                        <h2 class="portal-title">Fingerprint Voting System</h2>
                        <p class="portal-sub">Mandaue City College</p>
                    </div>

                    {{-- Fingerprint icon row --}}
                    <div class="fp-icon-row">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                        <span class="fp-label">Biometric Authentication</span>
                    </div>

                    @if(session('error'))
                        <div class="alert-error">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="divider"><span>Sign in as</span></div>

                    <div class="toggle-container">
                        <button class="tab-btn active" type="button" data-type="old" onclick="switchStudentType(this)">Old Student</button>
                        <button class="tab-btn" type="button" data-type="new" onclick="switchStudentType(this)">New Student</button>
                    </div>

                    {{-- Old Student Form --}}
                    <form id="form-old-student" action="{{ route('validate-login') }}" method="POST" autocomplete="off">
                        @csrf
                        <input type="hidden" name="student_type" value="Old Student">

                        <div class="input-group">
                            <label for="student_id_old">Student ID</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </span>
                                <input type="text" class="form-control" id="student_id_old" name="student_id" placeholder="STU-0**-0**">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password_old">Password</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </span>
                                <input type="password" class="form-control pr-icon" id="password_old" name="password" placeholder="Enter your password">
                                <button type="button" class="toggle-pass" onclick="togglePasswordVisibility('password_old', 'eyeIcon_old')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="eyeIcon_old">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <svg style="display:inline;vertical-align:-3px;margin-right:6px;" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Sign In
                        </button>
                    </form>

                    {{-- New Student Form --}}
                    <form id="form-new-student" action="{{ route('student.login') }}" method="POST" autocomplete="off" style="display: none;">
                        @csrf
                        <input type="hidden" name="student_type" value="New Student">

                        <div class="input-group">
                            <label for="student_id_new">Student ID</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </span>
                                <input type="text" class="form-control" id="student_id_new" name="student_id" placeholder="STU-0**-0**">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password_new">Create Password</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </span>
                                <input type="password" class="form-control pr-icon" id="password_new" name="password" placeholder="Create a strong password">
                                <button type="button" class="toggle-pass" onclick="togglePasswordVisibility('password_new', 'eyeIcon_new')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="eyeIcon_new">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password_confirm">Confirm Password</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </span>
                                <input type="password" class="form-control pr-icon" id="password_confirm" name="password_confirmation" placeholder="Confirm your password">
                                <button type="button" class="toggle-pass" onclick="togglePasswordVisibility('password_confirm', 'eyeIcon_confirm')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="eyeIcon_confirm">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <svg style="display:inline;vertical-align:-3px;margin-right:6px;" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Create Account
                        </button>
                    </form>

                    <p class="login-footer">
                        Having trouble? Contact your <a href="#">System Administrator</a>
                    </p>

                </div>{{-- /login-card --}}
            </div>
        </div>

    </div>

    <script>
        function togglePasswordVisibility(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const isPassword = passwordField.getAttribute("type") === "password";
            passwordField.setAttribute("type", isPassword ? "text" : "password");

            const eyeIconSvg = document.getElementById(iconId);
            if (isPassword) {
                eyeIconSvg.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            } else {
                eyeIconSvg.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            }
        }

        function switchStudentType(btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const type = btn.getAttribute('data-type');
            const formOld = document.getElementById('form-old-student');
            const formNew = document.getElementById('form-new-student');

            if (type === 'new') {
                formOld.style.display = 'none';
                formNew.style.display = 'block';
            } else {
                formOld.style.display = 'block';
                formNew.style.display = 'none';
            }
        }

        // Refresh CSRF token before any form submit to prevent 419
        async function refreshAndSubmit(form) {
            try {
                const res  = await fetch('/refresh-csrf');
                const data = await res.json();
                // Update all @csrf hidden inputs on the page
                document.querySelectorAll('input[name="_token"]').forEach(el => {
                    el.value = data.token;
                });
                // Update meta tag too
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
            } catch (e) {
                // If refresh fails, just submit anyway — token may still be valid
            }
            form.submit();
        }

        document.getElementById('form-old-student').addEventListener('submit', function (e) {
            e.preventDefault();
            refreshAndSubmit(this);
        });

        document.getElementById('form-new-student').addEventListener('submit', function (e) {
            e.preventDefault();
            refreshAndSubmit(this);
        });
    </script>
</body>

</html>
