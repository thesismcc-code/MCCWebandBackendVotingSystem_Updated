@extends('components.student-layout')
@section('title', 'Dashboard — MCC Student Portal')

@section('content')

@if($resultsPublished && !$isActive)
{{-- ── RESULTS PUBLISHED ── --}}
<div class="mb-5">
    <h1 class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-0.02em;">Official Results Published</h1>
    <p class="text-gray-500 text-sm mt-1">The election has concluded. View the final results below.</p>
</div>
<div class="s-card p-12 text-center">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
        <svg class="w-8 h-8" fill="none" stroke="#15803d" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
    </div>
    <h2 class="text-xl font-extrabold text-gray-900 mb-2">{{ $electionName }}</h2>
    <p class="text-gray-500 text-sm mb-6">Results have been officially published.</p>
    <a href="{{ route('view.student-results') }}"
        class="inline-flex items-center gap-2 px-8 py-3 rounded-full text-white font-bold text-sm transition-all hover:-translate-y-0.5 hover:shadow-lg"
        style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 16px rgba(26,92,56,.3);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        View Official Results
    </a>
</div>

@elseif($isActive)
{{-- ── ACTIVE ELECTION ── --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-5">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span>
            <span class="text-xs font-bold uppercase tracking-widest" style="color:#1a5c38;">Election Live</span>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-0.02em;">{{ $electionName }}</h1>
        <p class="text-gray-400 text-sm mt-0.5">Live vote counts update automatically every 30 seconds.</p>
    </div>
    @if($hasVoted)
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold" style="background:#dcfce7;color:#15803d;border:1px solid rgba(21,128,61,.2);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            You have voted
        </span>
    @else
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold" style="background:#fef9c3;color:#92400e;border:1px solid rgba(251,191,36,.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            You haven't voted yet
        </span>
    @endif
</div>

{{-- Turnout bar --}}
<div class="s-card p-5 mb-5">
    <div class="flex items-center justify-between mb-3">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Voter Turnout</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-0.5" style="letter-spacing:-0.02em;">{{ $turnoutPct }}%</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500"><span class="font-bold text-gray-800">{{ $votedCount }}</span> voted</p>
            <p class="text-sm text-gray-500"><span class="font-bold text-gray-800">{{ $totalVoters - $votedCount }}</span> not yet</p>
        </div>
    </div>
    <div style="height:8px;background:#e8f5ee;border-radius:99px;overflow:hidden;">
        <div style="height:100%;width:{{ min($turnoutPct,100) }}%;background:linear-gradient(90deg,#1a5c38,#4ade80);border-radius:99px;transition:width .7s;"></div>
    </div>
    <p class="text-xs text-gray-400 mt-2">{{ $totalVoters }} total eligible voters</p>
</div>

{{-- Live results --}}
@if(!empty($liveResults))
<div class="flex items-center gap-2 mb-4">
    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
    <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest">Live Vote Counts</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($liveResults as $position => $candidates)
    @php $maxVotes = max(array_column($candidates, 'votes') ?: [0]); @endphp
    <div class="s-card overflow-hidden">
        <div class="px-5 py-3.5 flex items-center gap-2" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <div class="w-1.5 h-1.5 rounded-full bg-green-300 animate-pulse"></div>
            <span class="text-[10px] font-bold text-white uppercase tracking-widest">{{ $position }}</span>
        </div>
        <div class="p-4 space-y-3">
            @foreach($candidates as $i => $c)
            @php
                $pct = $maxVotes > 0 ? round(($c['votes'] / $maxVotes) * 100) : 0;
                $isLeading = $i === 0 && $c['votes'] > 0;
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"
                             style="background:{{ $isLeading ? 'linear-gradient(135deg,#15803d,#16a34a)' : 'linear-gradient(135deg,#9ca3af,#6b7280)' }};">
                            {{ $isLeading ? '★' : strtoupper(substr($c['name'],0,1)) }}
                        </div>
                        <span class="text-sm font-semibold text-gray-800">{{ $c['name'] }}</span>
                        @if($isLeading)
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full" style="background:#dcfce7;color:#15803d;">Leading</span>
                        @endif
                    </div>
                    <span class="text-sm font-bold" style="color:{{ $isLeading ? '#1a5c38' : '#6b7280' }};">{{ $c['votes'] }} <span class="text-gray-400 font-normal text-xs">votes</span></span>
                </div>
                <div style="height:5px;background:#f0f4eb;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $isLeading ? 'linear-gradient(90deg,#1a5c38,#4ade80)' : '#d1d5db' }};border-radius:99px;transition:width .5s;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@else
<div class="s-card p-12 text-center">
    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#e8f5ee;">
        <svg class="w-7 h-7" fill="none" stroke="#2d7a52" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>
    <p class="text-gray-500 font-semibold">No votes have been cast yet.</p>
    <p class="text-gray-400 text-sm mt-1">Results will appear here once voting begins.</p>
</div>
@endif

@else
{{-- ── NO ACTIVE ELECTION ── --}}
<div class="mb-5">
    <h1 class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-0.02em;">Elections Coming Soon!</h1>
    <p class="text-gray-500 mt-1">Get excited — your vote will shape MCC's future leaders.</p>
</div>
<div class="s-card p-12 text-center relative overflow-hidden">
    <img src="{{ asset('icons/logo_white_bg.png') }}" alt=""
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 opacity-[0.03] pointer-events-none">
    <div class="relative z-10">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#e8f5ee;">
            <svg class="w-8 h-8" fill="none" stroke="#2d7a52" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
        </div>
        <p class="text-gray-600 font-semibold mb-5">No active election at the moment.</p>
        <button onclick="location.reload()"
            class="inline-flex items-center gap-2 px-8 py-3 rounded-full text-white font-bold text-sm transition-all hover:-translate-y-0.5 hover:shadow-lg"
            style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 16px rgba(26,92,56,.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh
        </button>
    </div>
</div>
@endif

@if($isActive)
<script>setTimeout(() => location.reload(), 30000);</script>
@endif
@endsection
