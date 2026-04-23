@extends('components.admin-layout')
@section('title', 'Dashboard — MCC Voting System')
@section('page-title', 'Dashboard')
@section('page-sub', 'Live overview, quick access & data exports')

@section('content')
<style>
/* ── Election Banner ── */
.election-banner {
    border-radius:20px; padding:18px 24px;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
    margin-bottom:20px; position:relative; overflow:hidden;
}
.election-banner::before {
    content:''; position:absolute; inset:0;
    background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 50%,#1a5c38 100%);
    z-index:0;
}
.election-banner::after {
    content:''; position:absolute; top:-40px; right:-40px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(255,255,255,.06); z-index:0;
}
.election-banner > * { position:relative; z-index:1; }
.banner-status {
    display:inline-flex; align-items:center; gap:6px;
    padding:4px 12px; border-radius:20px; font-size:11px; font-weight:800;
    letter-spacing:.05em; text-transform:uppercase;
}
.banner-status.active  { background:rgba(74,222,128,.2); color:#4ade80; border:1px solid rgba(74,222,128,.3); }
.banner-status.upcoming{ background:rgba(251,191,36,.2);  color:#fbbf24; border:1px solid rgba(251,191,36,.3); }
.banner-status.closed  { background:rgba(248,113,113,.2); color:#f87171; border:1px solid rgba(248,113,113,.3); }
.banner-status.none    { background:rgba(255,255,255,.1); color:rgba(255,255,255,.6); border:1px solid rgba(255,255,255,.15); }
.banner-pulse { width:7px; height:7px; border-radius:50%; background:#4ade80; animation:blink 1.4s infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
.countdown-box { display:flex; gap:8px; }
.cd-unit { text-align:center; background:rgba(255,255,255,.12); border-radius:10px; padding:6px 10px; min-width:48px; border:1px solid rgba(255,255,255,.15); }
.cd-num  { font-size:20px; font-weight:800; color:white; line-height:1; display:block; }
.cd-lbl  { font-size:9px; font-weight:700; color:rgba(255,255,255,.6); text-transform:uppercase; letter-spacing:.06em; }

/* ── Quick Access Pills ── */
.qa-strip { display:flex; flex-wrap:wrap; gap:7px; margin-bottom:22px; }
.qa-pill {
    display:inline-flex; align-items:center; gap:8px;
    padding:7px 13px; border-radius:11px; text-decoration:none;
    font-size:12px; font-weight:700; color:#374151;
    background:white; border:1px solid #e8ede3;
    box-shadow:0 1px 6px rgba(0,0,0,.05);
    transition:all .2s cubic-bezier(.4,0,.2,1); white-space:nowrap;
}
.qa-pill:hover { transform:translateY(-2px); box-shadow:0 5px 16px rgba(26,92,56,.15); border-color:#2d7a52; color:#1a5c38; }
.qa-pill .pill-icon { width:24px; height:24px; border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.qa-pill .pill-icon img { width:13px; height:13px; object-fit:contain; filter:brightness(0) invert(1); }

/* ── Section headers ── */
.sec-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.sec-lbl { display:flex; align-items:center; gap:7px; font-size:10.5px; font-weight:800; color:#9ab09a; text-transform:uppercase; letter-spacing:.1em; }
.live-dot { width:6px; height:6px; border-radius:50%; background:#22c55e; animation:blink 1.4s infinite; display:inline-block; }
.live-badge { display:inline-flex; align-items:center; gap:5px; background:#dcfce7; color:#15803d; font-size:10px; font-weight:800; padding:3px 10px; border-radius:20px; }
.ts-badge { font-size:10px; font-weight:700; color:#9ab09a; background:#f4f6f0; padding:3px 10px; border-radius:20px; }

/* ── Refresh bar ── */
.refresh-bar {
    display:flex; align-items:center; gap:10px;
    background:white; border:1px solid #e8ede3; border-radius:12px;
    padding:8px 14px; margin-bottom:16px;
    box-shadow:0 1px 6px rgba(0,0,0,.04);
}
.refresh-ring { width:28px; height:28px; position:relative; flex-shrink:0; }
.refresh-ring svg { width:28px; height:28px; transform:rotate(-90deg); }
.refresh-ring circle { fill:none; stroke:#e8ede3; stroke-width:3; }
.refresh-ring .progress { stroke:#2d7a52; stroke-linecap:round; transition:stroke-dashoffset .5s linear; }
.refresh-info { flex:1; }
.refresh-info .ri-label { font-size:11px; font-weight:700; color:#374151; }
.refresh-info .ri-time  { font-size:10px; font-weight:500; color:#9ab09a; }
.refresh-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 12px; border-radius:8px; font-size:11px; font-weight:700;
    background:linear-gradient(135deg,#1a5c38,#2d7a52); color:white; border:none; cursor:pointer;
    transition:all .2s; box-shadow:0 2px 8px rgba(26,92,56,.25);
}
.refresh-btn:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(26,92,56,.35); }
.refresh-btn.spinning svg { animation:spin .8s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }

/* ── Stat cards ── */
.stat-card-enhanced {
    background:white; border-radius:18px; padding:20px;
    border:1px solid #e8ede3; box-shadow:0 2px 12px rgba(0,0,0,.05);
    transition:all .2s; position:relative; overflow:hidden;
}
.stat-card-enhanced::after {
    content:''; position:absolute; bottom:0; left:0; right:0; height:3px; border-radius:0 0 18px 18px;
}
.stat-card-enhanced.blue::after  { background:linear-gradient(90deg,#3b82f6,#60a5fa); }
.stat-card-enhanced.green::after { background:linear-gradient(90deg,#16a34a,#4ade80); }
.stat-card-enhanced.yellow::after{ background:linear-gradient(90deg,#d97706,#fbbf24); }
.stat-card-enhanced.red::after   { background:linear-gradient(90deg,#dc2626,#f87171); }
.stat-card-enhanced:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,.1); }
.stat-val { font-size:32px; font-weight:800; color:#111827; line-height:1; letter-spacing:-0.02em; }
.stat-lbl { font-size:11px; font-weight:700; color:#9ab09a; text-transform:uppercase; letter-spacing:.07em; margin-top:4px; }

/* ── System health ── */
.health-card {
    background:white; border-radius:18px; padding:16px 20px;
    border:1px solid #e8ede3; box-shadow:0 2px 12px rgba(0,0,0,.05);
    display:flex; align-items:center; gap:12px;
}
.health-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.health-dot.online  { background:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.2); }
.health-dot.offline { background:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.2); }
.health-dot.checking{ background:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.2); animation:blink 1s infinite; }

/* ── Activity feed ── */
.activity-feed { background:white; border-radius:18px; border:1px solid #e8ede3; box-shadow:0 2px 12px rgba(0,0,0,.05); overflow:hidden; }
.activity-item { display:flex; align-items:flex-start; gap:10px; padding:11px 16px; border-bottom:1px solid #f4f6f0; transition:background .15s; }
.activity-item:last-child { border-bottom:none; }
.activity-item:hover { background:#f8fdf9; }
.activity-avatar { width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#1a5c38,#2d7a52); display:flex; align-items:center; justify-content:center; color:white; font-size:11px; font-weight:800; flex-shrink:0; }
.activity-text { font-size:12px; font-weight:600; color:#374151; line-height:1.4; }
.activity-time { font-size:10px; font-weight:500; color:#9ab09a; margin-top:2px; }

/* ── Export cards ── */
.export-card {
    background:white; border-radius:16px; border:1px solid #e8ede3;
    box-shadow:0 2px 10px rgba(0,0,0,.04);
    padding:16px 18px; display:flex; align-items:center; gap:12px;
    transition:all .2s; position:relative; overflow:hidden;
}
.export-card:hover { transform:translateY(-2px); box-shadow:0 6px 22px rgba(0,0,0,.09); border-color:#c8dcc8; }
.ec-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.ec-title { font-size:13px; font-weight:800; color:#1a3a1a; }
.ec-desc  { font-size:11px; font-weight:500; color:#9ab09a; margin-top:1px; }
.ec-btn {
    display:inline-flex; align-items:center; gap:4px;
    padding:6px 13px; border-radius:9px; font-size:11px; font-weight:700;
    border:none; cursor:pointer; transition:all .2s; text-decoration:none; white-space:nowrap;
}
.ec-btn:hover { transform:translateY(-1px); filter:brightness(1.06); }
.ec-btn svg { width:12px; height:12px; }
</style>

{{-- ═══ ELECTION STATUS BANNER ═══ --}}
@php
    $elStatus   = $activeElection['status'] ?? 'none';
    $elName     = $activeElection['election_name'] ?? null;
    $elSem      = $activeElection['semester'] ?? '';
    $elYear     = $activeElection['academic_year'] ?? '';
    $elDateFrom = $activeElection['date_from'] ?? null;
    $elDateTo   = $activeElection['date_to'] ?? null;
    $elOpen     = $activeElection['opening_time'] ?? null;
    $elClose    = $activeElection['closing_time'] ?? null;
    $endDtStr   = ($elDateTo && $elClose) ? $elDateTo . ' ' . $elClose : null;
@endphp
<div class="election-banner mb-5">
    <div class="flex items-center gap-3 flex-wrap">
        @if($elName)
            <span class="banner-status {{ $elStatus }}">
                @if($elStatus === 'active') <span class="banner-pulse"></span> @endif
                {{ ucfirst($elStatus) }}
            </span>
            <div>
                <div style="font-size:16px;font-weight:800;color:white;letter-spacing:-0.01em;">{{ $elName }}</div>
                <div style="font-size:12px;color:rgba(255,255,255,.65);font-weight:500;margin-top:2px;">
                    {{ $elSem }}{{ $elYear ? ' · ' . $elYear : ' ' }}
                    @if($elDateFrom) · {{ \Carbon\Carbon::parse($elDateFrom)->format('M d, Y') }} @endif
                    @if($elOpen && $elClose) · {{ \Carbon\Carbon::parse($elOpen)->format('g:i A') }} – {{ \Carbon\Carbon::parse($elClose)->format('g:i A') }} @endif
                </div>
            </div>
        @else
            <span class="banner-status none">No Active Election</span>
            <div style="font-size:14px;font-weight:700;color:rgba(255,255,255,.7);">Set up an election in Election Control</div>
        @endif
    </div>
    @if($elStatus === 'active' && $endDtStr)
    <div class="countdown-box" id="countdownBox">
        <div class="cd-unit"><span class="cd-num" id="cd-h">--</span><span class="cd-lbl">Hrs</span></div>
        <div class="cd-unit"><span class="cd-num" id="cd-m">--</span><span class="cd-lbl">Min</span></div>
        <div class="cd-unit"><span class="cd-num" id="cd-s">--</span><span class="cd-lbl">Sec</span></div>
    </div>
    <script>
        (function(){
            var end = new Date("{{ $endDtStr }}").getTime();
            function tick(){
                var now = Date.now(), diff = end - now;
                if(diff <= 0){ document.getElementById("countdownBox").innerHTML = '<span style="color:#f87171;font-weight:800;font-size:13px;">Election Ended</span>'; return; }
                var h = Math.floor(diff/3600000), m = Math.floor((diff%3600000)/60000), s = Math.floor((diff%60000)/1000);
                document.getElementById("cd-h").textContent = String(h).padStart(2,"0");
                document.getElementById("cd-m").textContent = String(m).padStart(2,"0");
                document.getElementById("cd-s").textContent = String(s).padStart(2,"0");
            }
            tick(); setInterval(tick, 1000);
        })();
    </script>
    @endif
</div>

{{-- ═══ QUICK ACCESS PILLS ═══ --}}
<div class="sec-hdr" style="margin-bottom:8px;">
    <div class="sec-lbl">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Quick Access
    </div>
</div>
<div class="qa-strip">
    @php $qaItems = [
        ['label'=>'Manage Accounts',       'route'=>route('view.manage-accounts'),      'icon'=>'/icons/person.png',      'bg'=>'#1a5c38'],
        ['label'=>'Fingerprint Enrollment', 'route'=>route('view.finger-print'),         'icon'=>'/icons/fingerprint.png', 'bg'=>'#059669'],
        ['label'=>'Election Control',       'route'=>route('view.election-control'),     'icon'=>'/icons/settings.png',    'bg'=>'#d97706'],
        ['label'=>'Student Eligibility',    'route'=>route('view.student-eligibility'),  'icon'=>'/icons/beenhere.png',    'bg'=>'#0d9488'],
        ['label'=>'Voting Logs',            'route'=>route('view.voting-logs'),          'icon'=>'/icons/how_to_vote.png', 'bg'=>'#16a34a'],
        ['label'=>'Security Logs',          'route'=>route('view.security-logs'),        'icon'=>'/icons/earthquake.png',  'bg'=>'#e11d48'],
        ['label'=>'System Activity',        'route'=>route('view.system-activity'),      'icon'=>'/icons/earthquake.png',  'bg'=>'#15803d'],
        ['label'=>'Reports & Analytics',    'route'=>route('view.reports-and-analytics'),'icon'=>'/icons/chart_data.png', 'bg'=>'#065f46'],
    ]; @endphp
    @foreach($qaItems as $item)
    <a href="{{ $item['route'] }}" class="qa-pill">
        <span class="pill-icon" style="background:{{ $item['bg'] }};"><img src="{{ $item['icon'] }}" alt="{{ $item['label'] }}"></span>
        {{ $item['label'] }}
    </a>
    @endforeach
</div>

{{-- ═══ AUTO-REFRESH BAR ═══ --}}
<div class="refresh-bar" id="refreshBar">
    <div class="refresh-ring">
        <svg viewBox="0 0 28 28">
            <circle cx="14" cy="14" r="11"/>
            <circle cx="14" cy="14" r="11" class="progress" id="refreshRing"
                stroke-dasharray="69.1" stroke-dashoffset="0"/>
        </svg>
    </div>
    <div class="refresh-info">
        <div class="ri-label">Auto-refreshing every 30 seconds</div>
        <div class="ri-time">Last updated: <span id="lastUpdated">just now</span></div>
    </div>
    <button class="refresh-btn" id="refreshBtn" onclick="doRefresh()">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        Refresh Now
    </button>
</div>

{{-- ═══ STAT CARDS ═══ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $statCards = [
        ['id'=>'stat-voters',     'val'=>$data['stats_card_data']['total_register_voters'], 'lbl'=>'Registered Voters',  'color'=>'blue',   'icon'=>'user'],
        ['id'=>'stat-votes',      'val'=>$data['stats_card_data']['live_vote_cast'],         'lbl'=>'Live Votes Cast',     'color'=>'green',  'icon'=>'check'],
        ['id'=>'stat-candidates', 'val'=>$data['stats_card_data']['running_candidates'],     'lbl'=>'Running Candidates',  'color'=>'yellow', 'icon'=>'users'],
        ['id'=>'stat-turnout',    'val'=>$data['stats_card_data']['turn_out_rates']['turnout_percent'].'%', 'lbl'=>'Turnout Rate', 'color'=>'red', 'icon'=>'percent'],
    ];
    $iconMap = [
        'user'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'check'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'users'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'percent' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>',
    ];
    $colorMap = [
        'blue'   => ['bg'=>'#e8f5ee', 'ic'=>'#2d7a52'],
        'green'  => ['bg'=>'#dcfce7', 'ic'=>'#16a34a'],
        'yellow' => ['bg'=>'#fef9c3', 'ic'=>'#ca8a04'],
        'red'    => ['bg'=>'#fee2e2', 'ic'=>'#dc2626'],
    ];
    @endphp
    @foreach($statCards as $sc)
    @php $c = $colorMap[$sc['color']]; @endphp
    <div class="stat-card-enhanced {{ $sc['color'] }}">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $c['bg'] }};">
                <svg class="w-5 h-5" fill="none" stroke="{{ $c['ic'] }}" stroke-width="2" viewBox="0 0 24 24">{!! $iconMap[$sc['icon']] !!}</svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:{{ $c['bg'] }};color:{{ $c['ic'] }};">Live</span>
        </div>
        <div class="stat-val" id="{{ $sc['id'] }}">{{ $sc['val'] }}</div>
        <div class="stat-lbl">{{ $sc['lbl'] }}</div>
    </div>
    @endforeach
</div>


{{-- ═══ SYSTEM HEALTH ═══ --}}
<div class="sec-hdr">
    <div class="sec-lbl">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        System Health
    </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    <div class="health-card">
        <span class="health-dot checking" id="fpDot"></span>
        <div>
            <div style="font-size:13px;font-weight:700;color:#1a3a1a;">Fingerprint Device</div>
            <div style="font-size:11px;color:#9ab09a;font-weight:500;" id="fpStatus">Checking...</div>
        </div>
    </div>
    <div class="health-card">
        <span class="health-dot online"></span>
        <div>
            <div style="font-size:13px;font-weight:700;color:#1a3a1a;">Firebase Database</div>
            <div style="font-size:11px;color:#22c55e;font-weight:600;">Connected</div>
        </div>
    </div>
    <div class="health-card">
        <span class="health-dot online"></span>
        <div>
            <div style="font-size:13px;font-weight:700;color:#1a3a1a;">Laravel Application</div>
            <div style="font-size:11px;color:#22c55e;font-weight:600;">Running · v{{ app()->version() }}</div>
        </div>
    </div>
</div>

{{-- ═══ LIVE RESULTS + TURNOUT + ACTIVITY FEED ═══ --}}
<div class="sec-hdr">
    <div class="sec-lbl"><span class="live-dot"></span> Live Candidate Results</div>
    <span class="live-badge"><span class="live-dot" style="width:5px;height:5px;"></span>Live</span>
</div>
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 mb-6">
    <div class="lg:col-span-3 flex flex-col gap-4" id="candidateResults">
        @forelse($data['live_candidate_result'] as $position => $candidates)
            @include('components.dashboard.candidateposistioncard', ['position' => $position, 'candidates' => $candidates])
        @empty
            <div class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-gray-200 bg-gray-50/60 p-12 text-center">
                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-sm font-semibold text-gray-400">No candidate results available yet.</p>
            </div>
        @endforelse
    </div>
    <div class="lg:col-span-2 flex flex-col gap-4">
        <div id="turnoutCard" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            @include('components.dashboard.realtimeturnoutcard', ['turnout' => $data['realtime_turnout']])
        </div>
        <div id="yearLevelCard" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            @include('components.dashboard.yearlevelturnoutcard', ['yearLevels' => $data['per_year_level_turnout']])
        </div>

        {{-- Real Activity Feed --}}
        <div>
            <div class="sec-hdr" style="margin-bottom:8px;">
                <div class="sec-lbl"><span class="live-dot"></span> Recent Activity</div>
                <span class="ts-badge" id="activityCount">{{ count($recentActivity) }} events</span>
            </div>

            {{-- Low turnout alert --}}
            @php
                $turnoutPct = $data['stats_card_data']['turn_out_rates']['turnout_percent'] ?? 0;
                $hoursLeft  = null;
                if (!empty($activeElection['date_to']) && !empty($activeElection['closing_time'])) {
                    try {
                        $endDt     = \Carbon\Carbon::parse($activeElection['date_to'] . ' ' . $activeElection['closing_time']);
                        $hoursLeft = now()->diffInHours($endDt, false);
                    } catch(\Exception $e) {}
                }
            @endphp
            @if(($activeElection['status'] ?? '') === 'active' && $turnoutPct < 30 && $hoursLeft !== null && $hoursLeft <= 2 && $hoursLeft >= 0)
            <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:12px;padding:10px 14px;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span style="font-size:12px;font-weight:700;color:#92400e;">Only {{ $hoursLeft }}h left and turnout is {{ $turnoutPct }}% — consider sending reminders!</span>
            </div>
            @endif

            <div class="activity-feed" id="activityFeed">
                @forelse($recentActivity as $act)
                @php
                    $isVote   = ($act['type'] ?? '') === 'vote';
                    $isError  = in_array($act['type'] ?? '', ['error', 'critical']);
                    $avatarBg = $isVote ? 'linear-gradient(135deg,#1a5c38,#2d7a52)' : ($isError ? 'linear-gradient(135deg,#dc2626,#ef4444)' : 'linear-gradient(135deg,#0d9488,#0f766e)');
                    $initial  = $isVote ? 'V' : strtoupper(substr($act['user'] ?? 'S', 0, 1));
                    try { $timeAgo = \Carbon\Carbon::parse($act['time'])->diffForHumans(); } catch(\Exception $e) { $timeAgo = 'recently'; }
                @endphp
                <div class="activity-item">
                    <div class="activity-avatar" style="background:{{ $avatarBg }};">{{ $initial }}</div>
                    <div>
                        <div class="activity-text">{{ $act['message'] }}</div>
                        <div class="activity-time">{{ $timeAgo }}@if(!$isVote && isset($act['user'])) · {{ $act['user'] }}@endif</div>
                    </div>
                </div>
                @empty
                <div class="activity-item">
                    <div class="activity-avatar" style="background:#e5e7eb;color:#9ca3af;">—</div>
                    <div>
                        <div class="activity-text" style="color:#9ca3af;">No recent activity yet</div>
                        <div class="activity-time">Waiting for election activity...</div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ═══ DATA EXPORTS ═══ --}}
<div class="sec-hdr">
    <div class="sec-lbl">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Data Exports & Backups
    </div>
    <span class="ts-badge">All exports are timestamped</span>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-2">

    <div class="export-card">
        <div class="ec-icon" style="background:#e8f5ee;"><svg width="20" height="20" fill="none" stroke="#1a5c38" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">Voting Logs</div><div class="ec-desc">All votes cast in the election</div></div>
        <a href="{{ route('voting-logs.export-pdf') }}" target="_blank" class="ec-btn text-white" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 2px 8px rgba(26,92,56,.3);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>PDF
        </a>
    </div>

    <div class="export-card">
        <div class="ec-icon" style="background:#fef9c3;"><svg width="20" height="20" fill="none" stroke="#ca8a04" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">End of Election Report</div><div class="ec-desc">Final results & full summary</div></div>
        <div style="display:flex;gap:5px;">
            <a href="{{ route('view.reports-and-analytics-end-of-election') }}" target="_blank" class="ec-btn" style="background:#fef9c3;color:#92400e;border:1px solid #fde68a;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View
            </a>
            <a href="{{ route('end-of-election.export-pdf') }}" target="_blank" class="ec-btn text-white" style="background:linear-gradient(135deg,#b45309,#d97706);box-shadow:0 2px 8px rgba(180,83,9,.3);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>PDF
            </a>
        </div>
    </div>

    <div class="export-card">
        <div class="ec-icon" style="background:#ede9fe;"><svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">System Snapshot</div><div class="ec-desc">Full stats & turnout backup</div></div>
        <button onclick="exportUsersJSON()" class="ec-btn text-white" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);box-shadow:0 2px 8px rgba(109,40,217,.3);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>JSON
        </button>
    </div>

    <div class="export-card">
        <div class="ec-icon" style="background:#dcfce7;"><svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">Voter Turnout</div><div class="ec-desc">Year-level turnout breakdown</div></div>
        <button onclick="exportTurnoutCSV()" class="ec-btn text-white" style="background:linear-gradient(135deg,#15803d,#16a34a);box-shadow:0 2px 8px rgba(21,128,61,.3);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>CSV
        </button>
    </div>

    <div class="export-card">
        <div class="ec-icon" style="background:#fee2e2;"><svg width="20" height="20" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">Candidate Results</div><div class="ec-desc">Live vote counts per candidate</div></div>
        <button onclick="exportCandidatesCSV()" class="ec-btn text-white" style="background:linear-gradient(135deg,#b91c1c,#dc2626);box-shadow:0 2px 8px rgba(185,28,28,.3);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>CSV
        </button>
    </div>

    <div class="export-card">
        <div class="ec-icon" style="background:#f0f9ff;"><svg width="20" height="20" fill="none" stroke="#0284c7" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">Print Dashboard</div><div class="ec-desc">Print current live snapshot</div></div>
        <button onclick="window.print()" class="ec-btn text-white" style="background:linear-gradient(135deg,#0369a1,#0284c7);box-shadow:0 2px 8px rgba(3,105,161,.3);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>Print
        </button>
    </div>

    <div class="export-card">
        <div class="ec-icon" style="background:#f0fdf4;"><svg width="20" height="20" fill="none" stroke="#15803d" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">Voter List</div><div class="ec-desc">All students with voted/not voted status</div></div>
        <a href="{{ route('export.voter-list-csv') }}" class="ec-btn text-white" style="background:linear-gradient(135deg,#15803d,#16a34a);box-shadow:0 2px 8px rgba(21,128,61,.3);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>CSV
        </a>
    </div>

    <div class="export-card">
        <div class="ec-icon" style="background:#faf5ff;"><svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div class="flex-1 min-w-0"><div class="ec-title">Election History</div><div class="ec-desc">View all past elections & results</div></div>
        <a href="{{ route('view.election-history') }}" class="ec-btn text-white" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);box-shadow:0 2px 8px rgba(109,40,217,.3);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View
        </a>
    </div>

</div>


<script>
// ── Helpers ──────────────────────────────────────────────────
function dlFile(name, content, mime) {
    const b = new Blob([content],{type:mime}), u = URL.createObjectURL(b), a = document.createElement('a');
    a.href=u; a.download=name; document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(u);
}
function ts() { return new Date().toISOString().slice(0,19).replace(/[:T]/g,'-'); }

// ── Export functions ──────────────────────────────────────────
function exportUsersJSON() {
    const d = {
        exported_at: new Date().toISOString(),
        stats: {
            total_registered_voters: {{ $data['stats_card_data']['total_register_voters'] }},
            live_votes_cast:         {{ $data['stats_card_data']['live_vote_cast'] }},
            running_candidates:      {{ $data['stats_card_data']['running_candidates'] }},
            turnout_percent:         {{ $data['stats_card_data']['turn_out_rates']['turnout_percent'] }},
        },
        realtime_turnout: @json($data['realtime_turnout']),
        per_year_level_turnout: @json($data['per_year_level_turnout']),
    };
    dlFile(`mcc-snapshot-${ts()}.json`, JSON.stringify(d,null,2), 'application/json');
}
function exportTurnoutCSV() {
    const rows = [['Year Level','Total Students','Voted','Not Yet Voted','Turnout %']];
    @foreach($data['per_year_level_turnout'] as $row)
    rows.push(['{{ addslashes($row['year_level']) }}','{{ $row['total_students'] }}','{{ $row['voted'] }}','{{ $row['not_yet_voted'] ?? ($row['total_students'] - $row['voted']) }}','{{ $row['turnout_percent'] }}']);
    @endforeach
    dlFile(`mcc-turnout-${ts()}.csv`, rows.map(r=>r.map(v=>`"${v}"`).join(',')). join('\n'), 'text/csv');
}
function exportCandidatesCSV() {
    const rows = [['Position','Candidate Name','Votes','Percentage %']];
    @foreach($data['live_candidate_result'] as $position => $candidates)
        @foreach($candidates as $candidate)
        rows.push(['{{ addslashes($position) }}','{{ addslashes($candidate['name']) }}','{{ $candidate['votes'] }}','{{ $candidate['percentage'] }}']);
        @endforeach
    @endforeach
    dlFile(`mcc-candidates-${ts()}.csv`, rows.map(r=>r.map(v=>`"${v}"`).join(',')). join('\n'), 'text/csv');
}

// ── Fingerprint device health check ──────────────────────────
async function checkFpDevice() {
    const dot = document.getElementById('fpDot');
    const lbl = document.getElementById('fpStatus');
    try {
        const r = await fetch('/api/fingerprint/status');
        const d = await r.json();
        if (d.initialized) {
            dot.className = 'health-dot online';
            lbl.textContent = 'Online · Ready';
            lbl.style.color = '#22c55e';
        } else {
            dot.className = 'health-dot offline';
            lbl.textContent = d.service_offline ? 'Service Offline' : 'Not Initialized';
            lbl.style.color = '#ef4444';
        }
    } catch(e) {
        dot.className = 'health-dot offline';
        lbl.textContent = 'Unreachable';
        lbl.style.color = '#ef4444';
    }
}
checkFpDevice();
setInterval(checkFpDevice, 15000);

// ── Animated counter ─────────────────────────────────────────
function animateCount(el, from, to, suffix) {
    const dur = 600, steps = 20;
    const step = (to - from) / steps;
    let cur = from, i = 0;
    const t = setInterval(() => {
        cur += step; i++;
        el.textContent = Math.round(cur) + (suffix || '');
        if (i >= steps) { el.textContent = to + (suffix || ''); clearInterval(t); }
    }, dur / steps);
}

// ── Candidate results renderer ────────────────────────────────
function renderCandidateResults(results) {
    const container = document.getElementById('candidateResults');
    if (!container) return;
    if (!results || Object.keys(results).length === 0) {
        container.innerHTML = `<div class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-gray-200 bg-gray-50/60 p-12 text-center">
            <p class="text-sm font-semibold text-gray-400">No candidate results available yet.</p></div>`;
        return;
    }
    container.innerHTML = Object.entries(results).map(([position, candidates]) => {
        const rows = candidates.map((c, idx) => {
            const isLeading = idx === 0 && c.votes > 0;
            const pct = Math.min(c.percentage, 100);
            return `<div class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50/60 transition-colors" style="${isLeading ? 'background:#f0fdf4;' : ''}">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"
                     style="background:${isLeading ? 'linear-gradient(135deg,#15803d,#16a34a)' : 'linear-gradient(135deg,#2d7a52,#1a5c38)'};">
                    ${isLeading ? '★' : c.name.charAt(0).toUpperCase()}
                </div>
                <span class="flex-1 text-sm font-semibold truncate" style="color:${isLeading ? '#15803d' : '#1f2937'};">${c.name}</span>
                <span class="text-sm font-bold tabular-nums" style="color:#1a5c38;min-width:40px;text-align:right;">${c.votes.toLocaleString()}</span>
                <div class="flex items-center gap-2" style="width:140px;">
                    <div class="flex-1 rounded-full overflow-hidden" style="height:6px;background:#e8f5ee;">
                        <div style="height:100%;width:${pct}%;background:${isLeading ? 'linear-gradient(90deg,#15803d,#4ade80)' : 'linear-gradient(90deg,#2d7a52,#4CAF7D)'};border-radius:99px;transition:width .7s;"></div>
                    </div>
                    <span class="text-[11px] font-bold tabular-nums" style="color:#6b9e6b;width:34px;text-align:right;">${c.percentage}%</span>
                </div>
            </div>`;
        }).join('');
        return `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100" style="background:#f8faf6;">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full" style="background:#2d7a52;"></div>
                    <span class="font-extrabold text-xs text-gray-700 uppercase tracking-widest">${position}</span>
                </div>
                <div class="flex items-center gap-6">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider w-14 text-center">Votes</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider w-36 text-center">Progress</span>
                </div>
            </div>
            <div class="divide-y divide-gray-50">${rows}</div>
        </div>`;
    }).join('');
}

// ── Turnout card renderer ─────────────────────────────────────
function renderTurnout(t) {
    const card = document.getElementById('turnoutCard');
    if (!card || !t) return;
    const pct  = t.turnout_percent ?? 0;
    const dash = (pct / 100) * 99.9;
    card.innerHTML = `
        <div class="flex items-center justify-between mb-5">
            <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest">Real-Time Turnout</h4>
            <span class="flex items-center gap-1.5 text-[10px] font-bold px-2 py-1 rounded-full" style="background:#dcfce7;color:#15803d;">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>Live
            </span>
        </div>
        <div class="flex items-center gap-5 mb-5">
            <div class="relative flex-shrink-0" style="width:80px;height:80px;">
                <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8f5ee" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#2d7a52" stroke-width="3"
                        stroke-dasharray="${dash},100" stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:14px;font-weight:800;color:#1a5c38;">${pct}%</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 flex-1">
                <div><div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Total</div><div class="text-xl font-extrabold text-gray-900">${(t.total_students||0).toLocaleString()}</div></div>
                <div><div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Turnout</div><div class="text-xl font-extrabold" style="color:#1a5c38;">${pct}%</div></div>
                <div><div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Voted</div><div class="text-xl font-extrabold text-green-600">${(t.voted_count||0).toLocaleString()}</div></div>
                <div><div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Not Yet</div><div class="text-xl font-extrabold text-orange-500">${(t.not_yet_voted||0).toLocaleString()}</div></div>
            </div>
        </div>
        <div style="height:6px;background:#e8f5ee;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:${Math.min(pct,100)}%;background:linear-gradient(90deg,#2d7a52,#4CAF7D);border-radius:99px;transition:width .7s;"></div>
        </div>`;
}

// ── Year level renderer ───────────────────────────────────────
function renderYearLevels(yearLevels) {
    const card = document.getElementById('yearLevelCard');
    if (!card) return;
    if (!yearLevels || !yearLevels.length) {
        card.innerHTML = `<h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-5">Year Level Turnout</h4>
            <div class="text-center py-6 text-gray-300"><p class="text-xs font-medium">No data yet</p></div>`;
        return;
    }
    const rows = yearLevels.map(row => {
        const p = row.turnout_percent ?? 0;
        const color = p >= 70 ? '#16a34a' : (p >= 40 ? '#2d7a52' : '#f59e0b');
        return `<div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-bold text-gray-700">${row.year_level}</span>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-gray-400">${(row.voted||0).toLocaleString()}/${(row.total_students||0).toLocaleString()}</span>
                    <span class="text-xs font-extrabold tabular-nums" style="color:${color};">${p}%</span>
                </div>
            </div>
            <div style="height:6px;background:#f0f4eb;border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:${Math.min(p,100)}%;background:${color};border-radius:99px;transition:width .7s;"></div>
            </div>
        </div>`;
    }).join('');
    card.innerHTML = `<h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-5">Year Level Turnout</h4>
        <div class="space-y-4">${rows}</div>`;
}

// ── Activity feed renderer ────────────────────────────────────
function renderActivity(activities) {
    const feed = document.getElementById('activityFeed');
    if (!feed || !activities) return;
    if (!activities.length) {
        feed.innerHTML = `<div class="activity-item">
            <div class="activity-avatar" style="background:#e5e7eb;color:#9ca3af;">—</div>
            <div><div class="activity-text" style="color:#9ca3af;">No recent activity yet</div>
            <div class="activity-time">Waiting for election activity...</div></div></div>`;
        return;
    }
    const typeColors = { vote:'linear-gradient(135deg,#1a5c38,#2d7a52)', error:'linear-gradient(135deg,#dc2626,#ef4444)', critical:'linear-gradient(135deg,#dc2626,#ef4444)' };
    feed.innerHTML = activities.map(a => {
        const bg   = typeColors[a.type] || 'linear-gradient(135deg,#0d9488,#0f766e)';
        const init = a.type === 'vote' ? 'V' : (a.user || 'S').charAt(0).toUpperCase();
        const user = (a.type !== 'vote' && a.user) ? ' · ' + a.user : '';
        return `<div class="activity-item">
            <div class="activity-avatar" style="background:${bg};">${init}</div>
            <div><div class="activity-text">${a.message}</div>
            <div class="activity-time">${a.time_ago || a.time}${user}</div></div>
        </div>`;
    }).join('');
    const cnt = document.getElementById('activityCount');
    if (cnt) cnt.textContent = activities.length + ' events';
}

// ── Auto-refresh with ring countdown ─────────────────────────
const REFRESH_INTERVAL = 30;
let countdown = REFRESH_INTERVAL;
let refreshing = false;
const ring = document.getElementById('refreshRing');
const circumference = 69.1;

function updateRing() {
    const offset = circumference * (1 - countdown / REFRESH_INTERVAL);
    ring.style.strokeDashoffset = offset;
}

async function doRefresh() {
    if (refreshing) return;
    refreshing = true;
    const btn = document.getElementById('refreshBtn');
    btn.classList.add('spinning');
    btn.disabled = true;
    try {
        const r = await fetch('{{ route("dashboard.refresh") }}');
        if (!r.ok) throw new Error('HTTP ' + r.status);
        const d = await r.json();

        // Animate stat card updates
        const voters = document.getElementById('stat-voters');
        const votes  = document.getElementById('stat-votes');
        const cands  = document.getElementById('stat-candidates');
        const turn   = document.getElementById('stat-turnout');
        animateCount(voters, parseInt(voters.textContent)  || 0, d.stats.total_register_voters, '');
        animateCount(votes,  parseInt(votes.textContent)   || 0, d.stats.live_vote_cast, '');
        animateCount(cands,  parseInt(cands.textContent)   || 0, d.stats.running_candidates, '');
        animateCount(turn,   parseFloat(turn.textContent)  || 0, d.stats.turnout_percent, '%');

        // Re-render ALL live sections
        renderCandidateResults(d.live_candidate_result);
        renderTurnout(d.realtime_turnout);
        renderYearLevels(d.per_year_level_turnout);
        if (d.recent_activity) renderActivity(d.recent_activity);

        // Flash refresh bar green on success
        const bar = document.getElementById('refreshBar');
        bar.style.borderColor = '#22c55e';
        bar.style.background  = '#f0fdf4';
        setTimeout(() => { bar.style.borderColor = ''; bar.style.background = ''; }, 1200);

        document.getElementById('lastUpdated').textContent = d.refreshed_at;
        countdown = REFRESH_INTERVAL;
        updateRing();
    } catch(e) {
        console.warn('Refresh failed', e);
        const bar = document.getElementById('refreshBar');
        bar.style.borderColor = '#ef4444';
        setTimeout(() => { bar.style.borderColor = ''; }, 1200);
    }
    btn.classList.remove('spinning');
    btn.disabled = false;
    refreshing = false;
}

setInterval(() => {
    countdown--;
    if (countdown <= 0) { doRefresh(); return; }
    updateRing();
}, 1000);
updateRing();
</script>

@endsection



