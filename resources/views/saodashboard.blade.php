@extends('components.sao-layout')
@section('title', 'SAO Dashboard — MCC Voting System')
@section('page-title', 'Dashboard')
@section('page-sub', 'Election overview and voter turnout')

@section('content')

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
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide">Total Voters</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#16a34a,#4ade80);"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#dcfce7;">
                <svg class="w-5 h-5" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:#dcfce7;color:#16a34a;">Live</span>
        </div>
        <div class="text-3xl font-extrabold text-gray-900 leading-none mb-1">{{ number_format($voted) }}</div>
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
        <div class="text-3xl font-extrabold text-gray-900 leading-none mb-1">{{ number_format($notYet) }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide">Not Yet Voted</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#0d9488,#2dd4bf);"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#ccfbf1;">
                <svg class="w-5 h-5" fill="none" stroke="#0d9488" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:#ccfbf1;color:#0d9488;">Live</span>
        </div>
        <div class="text-3xl font-extrabold text-gray-900 leading-none mb-1">{{ $pct }}%</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide">Turnout Rate</div>
    </div>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- Election Overview --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest">Election Overview</h4>
            @php
                $statusColor = match($electionStatus) {
                    'active'   => ['bg'=>'#dcfce7','color'=>'#15803d'],
                    'upcoming' => ['bg'=>'#fef9c3','color'=>'#92400e'],
                    'closed'   => ['bg'=>'#f3f4f6','color'=>'#6b7280'],
                    default    => ['bg'=>'#f3f4f6','color'=>'#6b7280'],
                };
            @endphp
            <span class="text-[10px] font-bold px-3 py-1 rounded-full" style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};">
                @if($electionStatus === 'active') <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#22c55e;margin-right:4px;animation:pulse 1.4s infinite;"></span> @endif
                {{ ucfirst($electionStatus) }}
            </span>
        </div>
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-4 rounded-xl" style="background:#f8faf6;">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Election Name</div>
                    <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $electionName }}</div>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl" style="background:#f8faf6;">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#0d9488,#0f766e);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Election Status</div>
                    <div class="text-sm font-bold text-gray-900 mt-0.5">{{ ucfirst($electionStatus) }}</div>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl" style="background:#f8faf6;">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Results Status</div>
                    <div class="text-sm font-bold mt-0.5" style="color:#dc2626;">Not Published</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Voter Turnout --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest">Real-Time Turnout</h4>
            <span class="flex items-center gap-1.5 text-[10px] font-bold px-2 py-1 rounded-full" style="background:#dcfce7;color:#15803d;">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                Live
            </span>
        </div>

        {{-- Circular progress --}}
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
</div>

{{-- Year Level Turnout --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-5">
    <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-5">Year Level Turnout</h4>
    @if(empty($byYear))
        <div class="text-center py-6 text-gray-300">
            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-xs font-medium">No data yet</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($byYear as $yl)
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
    @endif
</div>

<style>
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:.4} }
</style>
@endsection
