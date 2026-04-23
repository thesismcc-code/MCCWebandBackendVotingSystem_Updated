<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MCC Voting System — Student')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f4f6f0; min-height: 100vh; }

        /* ── Navbar ── */
        .s-navbar {
            background: white;
            border-bottom: 1px solid #e8ede3;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }
        .s-nav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 32px; height: 68px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .s-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .s-brand img { width: 42px; height: 42px; border-radius: 12px; object-fit: cover; box-shadow: 0 3px 10px rgba(26,92,56,.2); }
        .s-brand-text { font-size: 15px; font-weight: 800; color: #1a3a1a; letter-spacing: -0.02em; }
        .s-brand-sub  { font-size: 11px; font-weight: 600; color: #6b9e6b; text-transform: uppercase; letter-spacing: .04em; }

        .s-nav-right { display: flex; align-items: center; gap: 12px; }
        .s-nav-btn {
            width: 40px; height: 40px; border-radius: 50%;
            background: #f4f6f0; border: 1px solid #e8ede3;
            display: flex; align-items: center; justify-content: center;
            color: #1a3a1a; cursor: pointer; transition: all .2s; text-decoration: none;
        }
        .s-nav-btn:hover { background: #e8f5ee; border-color: #2d7a52; color: #1a5c38; transform: scale(1.05); }
        .s-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, #2d7a52, #1a5c38);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 15px;
            box-shadow: 0 3px 10px rgba(26,92,56,.25);
            text-decoration: none;
        }

        /* ── Page wrapper ── */
        .s-main { max-width: 1100px; margin: 0 auto; padding: 32px 32px; }

        /* ── Cards ── */
        .s-card {
            background: white; border-radius: 20px;
            border: 1px solid #e8ede3;
            box-shadow: 0 2px 16px rgba(0,0,0,.05);
            overflow: hidden;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c8dcc8; border-radius: 10px; }

        @media(max-width:768px) {
            .s-nav-inner, .s-main { padding-left: 16px; padding-right: 16px; }
        }
    </style>
    @yield('head')
</head>
<body>

<nav class="s-navbar">
    <div class="s-nav-inner">
        <a href="{{ route('view.student-dashboard') }}" class="s-brand">
            <img src="{{ asset('icons/logo_white_bg.png') }}" alt="MCC">
            <div>
                <div class="s-brand-text">MCC Voting</div>
                <div class="s-brand-sub">Student Portal</div>
            </div>
        </a>
        <div class="s-nav-right">
            <a href="{{ route('view.student-dashboard') }}" class="s-nav-btn" title="Dashboard">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            </a>
            <a href="{{ route('view.student-profile') }}" class="s-avatar" title="Profile">
                {{ strtoupper(substr(session('auth_user.first_name', 'S'), 0, 1)) }}
            </a>
            <form action="{{ route('student.logout') }}" method="POST">
                @csrf
                <button type="submit" class="s-nav-btn" title="Logout">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</nav>

<main class="s-main">
    @yield('content')
</main>

</body>
</html>
