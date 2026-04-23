@extends('components.comelec-layout')
@section('title', 'Comelec Dashboard — MCC Voting System')
@section('page-title', 'Dashboard')
@section('page-sub', 'Live election overview and voter turnout')

@section('content')

{{-- Election Status Banner --}}
<div class="rounded-2xl p-5 mb-6 flex items-center justify-between flex-wrap gap-3 relative overflow-hidden"
     style="background:linear-gradient(135deg,#1a5c38 0%,#2d7a52 50%,#1a5c38 100%);">
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);"></div>
    <div class="flex items-center gap-3 relative z-10">
        @if($electionStatus === 'Active')
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-800" style="background:rgba(74,222,128,.2);color:#4ade80;border:1px solid rgba(74,222,128,.3);">
                <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;animation:blink 1.4s infinite;display:inline-block;"></span>
                Active
            </span>
            <div>
                <div style="font-size:16px;font-weight:800;color:white;letter-spacing:-0.01em;">{{ $electionName ?? 'Active Election' }}</div>
                <div style="font-size:12px;color:rgba(255,255,255,.6);font-weight:500;margin-top:2px;">Election is currently running</div>
            </div>
        @else
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-800" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.6);border:1px solid rgba(255,255,255,.15);">
                No Active Election
            </span>
            <div style="font-size:14px;font-weight:700;color:rgba(255,255,255,.7);">Set up an election in Election Control</div>
        @endif
    </div>
    <a href="{{ route('view.comelec-manage-candidates') }}"
       class="relative z-10 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-0.5"
       style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Manage Candidates
    </a>
</div>

{{-- Stat Cards --}}
@php
    $pct    = $turnout['turnout_percent'] ?? 0;
    $total  = $turnout['total_students']  ?? 0;
    $voted  = $turnout['voted_count']     ?? 0;
    $notYet = $turnout['not_yet_voted']   ?? 0;
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#1a5c38,#2d7a52,#4ade80);"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#e8f5ee;">
                <svg class="w-5 h-5" fill="none" stroke="#2d7a52" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:#e8f5ee;color:#2d7a52;">Live</span>
        </div>
        <div class="text-3xl font-extrabold text-gray-900 leading-none mb-1">{{ number_format($total) }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide">Total Students</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#16a34a,#4ade80);"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#dcfce7;">
                <svg class="w-5 h-5" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:#dcfce7;color:#16a34a;">Live</span>
        </div>
        <div class="text-3xl font-extrabold text-green-600 leading-none mb-1">{{ number_format($voted) }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide">Voted</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#fef9c3;">
                <svg class="w-5 h-5" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:#fef9c3;color:#d97706;">Live</span>
        </div>
        <div class="text-3xl font-extrabold text-orange-500 leading-none mb-1">{{ number_format($notYet) }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide">Not Yet Voted</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#d97706,#f59e0b);"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#fef9c3;">
                <svg class="w-5 h-5" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:#fef9c3;color:#d97706;">Live</span>
        </div>
        <div class="text-3xl font-extrabold text-gray-900 leading-none mb-1">{{ $candidateCount }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide">Candidates</div>
    </div>
</div>

{{-- Main Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Real-Time Turnout --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest">Real-Time Voter Turnout</h4>
            <span class="flex items-center gap-1.5 text-[10px] font-bold px-2 py-1 rounded-full" style="background:#dcfce7;color:#15803d;">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                Live
            </span>
        </div>
        <div class="flex items-center gap-5 mb-5">
            <div class="relative flex-shrink-0" style="width:80px;height:80px;">
                <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8f5ee" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#2d7a52" stroke-width="3"
                        stroke-dasharray="{{ $pct }},100" stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:14px;font-weight:800;color:#1a5c38;">{{ $pct }}%</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 flex-1">
                <div>
                    <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Total</div>
                    <div class="text-xl font-extrabold text-gray-900">{{ number_format($total) }}</div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Turnout</div>
                    <div class="text-xl font-extrabold" style="color:#1a5c38;">{{ $pct }}%</div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Voted</div>
                    <div class="text-xl font-extrabold text-green-600">{{ number_format($voted) }}</div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Not Yet</div>
                    <div class="text-xl font-extrabold text-orange-500">{{ number_format($notYet) }}</div>
                </div>
            </div>
        </div>
        <div style="height:6px;background:#e8f5ee;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ min($pct,100) }}%;background:linear-gradient(90deg,#2d7a52,#4CAF7D);border-radius:99px;transition:width .7s;"></div>
        </div>
    </div>

    {{-- Year Level Turnout --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-5">Per Year Level Turnout</h4>
        @if(empty($yearLevelData))
            <div class="text-center py-10 text-gray-300">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-xs font-medium">No data yet</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($yearLevelData as $yl)
                @php
                    $p = $yl['turnout_percent'] ?? 0;
                    $barColor = $p >= 70 ? '#16a34a' : ($p >= 40 ? '#2d7a52' : '#f59e0b');
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-gray-700">{{ $yl['year_level'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-gray-400">{{ number_format($yl['voted'] ?? 0) }}/{{ number_format($yl['total_students'] ?? 0) }}</span>
                            <span class="text-xs font-extrabold tabular-nums" style="color:{{ $barColor }};">{{ $p }}%</span>
                        </div>
                    </div>
                    <div style="height:6px;background:#f0f4eb;border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:{{ min($p,100) }}%;background:{{ $barColor }};border-radius:99px;transition:width .7s;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-5 pt-4 border-t border-gray-100 text-xs text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>≥70%</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full inline-block" style="background:#2d7a52;"></span>40–69%</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>&lt;40%</span>
            </div>
        @endif
    </div>
</div>

<p class="text-center text-gray-400 text-xs mt-5">Data refreshes automatically every 30 seconds</p>

<style>
@keyframes blink { 0%,100%{opacity:1}50%{opacity:.4} }
</style>
<script>setTimeout(() => location.reload(), 30000);</script>
@endsection
