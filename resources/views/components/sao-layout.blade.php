<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MCC Voting System — SAO')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f4f6f0; }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ffffff 0%, #fafbf9 100%);
            border-right: 1px solid #e8ede3;
            display: flex; flex-direction: column;
            height: 100vh; position: fixed; left: 0; top: 0;
            z-index: 50;
            box-shadow: 6px 0 32px rgba(0,0,0,.06);
        }
        .sidebar-logo {
            padding: 28px 24px 24px;
            border-bottom: 1px solid #f0f4eb;
            display: flex; align-items: center; gap: 14px;
            background: linear-gradient(135deg, #ffffff, #f8faf7);
        }
        .sidebar-logo img { width: 48px; height: 48px; border-radius: 14px; object-fit: cover; box-shadow: 0 4px 16px rgba(26,92,56,.15); }
        .sidebar-logo-text .name { font-size: 15px; font-weight: 800; color: #1a3a1a; letter-spacing: -0.02em; }
        .sidebar-logo-text .role { font-size: 11px; font-weight: 600; color: #6b9e6b; letter-spacing: .04em; text-transform: uppercase; }

        .nav-section-label {
            font-size: 10px; font-weight: 800; color: #8fb08f;
            text-transform: uppercase; letter-spacing: .1em;
            padding: 20px 24px 8px; position: relative;
        }
        .nav-section-label::after {
            content: ''; position: absolute; bottom: 4px; left: 24px; right: 24px;
            height: 1px; background: linear-gradient(90deg, #e8ede3, transparent);
        }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; margin: 3px 12px;
            border-radius: 12px; font-size: 14px; font-weight: 600;
            color: #5a7a5a; text-decoration: none;
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent; position: relative; overflow: hidden;
        }
        .nav-link::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, #f0f7f0, #e8f5e8); opacity: 0; transition: opacity .2s ease;
        }
        .nav-link:hover::before { opacity: 1; }
        .nav-link:hover { color: #1a5c38; transform: translateX(2px); box-shadow: 0 2px 8px rgba(26,92,56,.1); }
        .nav-link.active {
            background: linear-gradient(135deg, #1a5c38, #2d7a52);
            color: #fff; border-color: transparent;
            box-shadow: 0 6px 20px rgba(26,92,56,.3); transform: translateX(2px);
        }
        .nav-link.active::before { display: none; }
        .nav-link .icon { width: 20px; height: 20px; flex-shrink: 0; opacity: .85; position: relative; z-index: 1; }
        .nav-link.active .icon { opacity: 1; }
        .nav-link span { position: relative; z-index: 1; }

        .main-wrap { margin-left: 280px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            background: linear-gradient(135deg, #ffffff, #fafbf9);
            border-bottom: 1px solid #e8ede3;
            padding: 0 36px; height: 72px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 40;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }
        .topbar-title { font-size: 20px; font-weight: 800; color: #1a3a1a; letter-spacing: -0.02em; }
        .topbar-sub { font-size: 13px; color: #8fb08f; font-weight: 500; margin-top: 2px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-box {
            text-align: right; padding: 8px 16px;
            background: rgba(26,92,56,.05); border-radius: 12px; border: 1px solid rgba(26,92,56,.1);
        }
        .clock-box .time { font-size: 16px; font-weight: 800; color: #1a3a1a; letter-spacing: .02em; }
        .clock-box .date { font-size: 11px; color: #8fb08f; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: linear-gradient(135deg, #2d7a52, #1a5c38);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 800; font-size: 16px;
            box-shadow: 0 4px 16px rgba(26,92,56,.25); border: 2px solid rgba(255,255,255,.8);
        }
        .page-content { flex: 1; padding: 32px 36px; }
        .content-card {
            background: linear-gradient(135deg, #ffffff, #fafbf9);
            border-radius: 24px; border: 1px solid #e8ede3;
            box-shadow: 0 4px 24px rgba(0,0,0,.06);
            padding: 32px; min-height: calc(100vh - 180px);
            position: relative; overflow: hidden;
        }
        .content-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(26,92,56,.1), transparent);
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #c8dcc8, #b8d0b8); border-radius: 10px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('icons/logo.png') }}" alt="MCC">
        <div class="sidebar-logo-text">
            <div class="name">MCC Voting</div>
            <div class="role">SAO Head</div>
        </div>
    </div>

    <div style="flex:1; overflow-y:auto; padding-bottom:16px;">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('view.sao-dashboard') }}" class="nav-link {{ request()->is('sao-dashboard*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            <span>Dashboard</span>
        </a>

        <div class="nav-section-label">Election</div>
        <a href="{{ route('view.sao-candidate-list') }}" class="nav-link {{ request()->is('sao-candidate-list*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Candidate List</span>
        </a>
        <a href="{{ route('view.sao-voter-participation') }}" class="nav-link {{ request()->is('sao-voter-participation*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Voter Participation</span>
        </a>
        <a href="{{ route('view.sao-final-results') }}" class="nav-link {{ request()->is('sao-final-results*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Final Results</span>
        </a>
    </div>

    <div style="padding:12px 8px; border-top:1px solid #f0f4eb;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="width:100%; background:none; border:none; cursor:pointer; color:#e05252;">
                <svg class="icon" style="color:#e05252;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<div class="main-wrap">
    <header class="topbar">
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-sub">@yield('page-sub', 'MCC Digital Voting System — SAO')</div>
        </div>
        <div class="topbar-right">
            <div class="clock-box">
                <div id="clock" class="time"></div>
                <div id="date" class="date"></div>
            </div>
            <div class="avatar">S</div>
        </div>
    </header>

    <div class="page-content">
        <div class="content-card">
            @yield('content')
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        document.getElementById('date').textContent  = now.toLocaleDateString('en-GB');
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
</body>
</html>
