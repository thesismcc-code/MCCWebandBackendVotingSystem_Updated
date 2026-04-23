@extends('components.admin-layout')
@section('title', 'Reports & Analytics — MCC Voting System')
@section('page-title', 'Reports & Analytics')
@section('page-sub', 'Real-time summary, statistics, and results')

@section('content')

{{-- Top Action --}}
<div class="flex justify-end mb-6">
    <a href="{{ route('view.reports-and-analytics-end-of-election') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all"
       style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        End of Election Reports
    </a>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Total Voters</p>
        <p class="text-3xl font-extrabold" style="color:#1a5c38;">{{ $turnout['total_students'] ?? 0 }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Votes Cast</p>
        <p class="text-3xl font-extrabold" style="color:#1a5c38;">{{ $turnout['voted_count'] ?? 0 }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Positions</p>
        <p class="text-3xl font-extrabold" style="color:#1a5c38;">{{ $positions }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Candidates</p>
        <p class="text-3xl font-extrabold" style="color:#1a5c38;">{{ $candidates }}</p>
    </div>
    {{-- Turnout circular --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex flex-col items-center justify-center">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Turnout</p>
        @php $pct = $turnout['turnout_percent'] ?? 0; $r = 28; $circ = 2 * M_PI * $r; $dash = ($pct / 100) * $circ; @endphp
        <div class="relative w-16 h-16">
            <svg class="w-16 h-16 -rotate-90" viewBox="0 0 72 72">
                <circle cx="36" cy="36" r="{{ $r }}" fill="none" stroke="#e8ede3" stroke-width="6"/>
                <circle cx="36" cy="36" r="{{ $r }}" fill="none" stroke="#2d7a52" stroke-width="6"
                        stroke-dasharray="{{ round($dash, 2) }} {{ round($circ, 2) }}" stroke-linecap="round"/>
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-sm font-extrabold" style="color:#1a5c38;">{{ $pct }}%</span>
        </div>
    </div>
</div>

{{-- Detail Panels --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

    {{-- Real Time Voter Turnout --}}
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-1 h-5 rounded-full" style="background:#1a5c38;"></div>
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#1a5c38;">Real Time Voter Turnout</h3>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-[#f4f6f0] rounded-xl p-4">
                <p class="text-xs text-gray-400 font-semibold mb-1">Total Voters</p>
                <p class="text-2xl font-extrabold text-gray-900">{{ $turnout['total_students'] ?? 0 }}</p>
            </div>
            <div class="bg-[#f4f6f0] rounded-xl p-4">
                <p class="text-xs text-gray-400 font-semibold mb-1">Turnout</p>
                <p class="text-2xl font-extrabold" style="color:#1a5c38;">{{ $turnout['turnout_percent'] ?? 0 }}%</p>
            </div>
            <div class="bg-[#f4f6f0] rounded-xl p-4">
                <p class="text-xs text-gray-400 font-semibold mb-1">Voted</p>
                <p class="text-2xl font-extrabold text-gray-900">{{ $turnout['voted_count'] ?? 0 }}</p>
            </div>
            <div class="bg-[#f4f6f0] rounded-xl p-4">
                <p class="text-xs text-gray-400 font-semibold mb-1">Not Yet</p>
                <p class="text-2xl font-extrabold text-gray-900">{{ $turnout['not_yet_voted'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Per Year Level Turnout --}}
    <div class="lg:col-span-3 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-1 h-5 rounded-full" style="background:#1a5c38;"></div>
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#1a5c38;">Per Year Level Turnout</h3>
        </div>
        @php $barColors = ['#1a5c38','#2d7a52','#4CAF7D','#81c99e','#b2dfc3']; @endphp
        <div class="flex flex-col gap-4">
            @forelse($byYear as $i => $yl)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-semibold text-gray-700">{{ $yl['year_level'] }}</span>
                    <span class="text-sm font-bold" style="color:#1a5c38;">{{ $yl['turnout_percent'] }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-500"
                         style="width:{{ $yl['turnout_percent'] }}%; background:{{ $barColors[$i % count($barColors)] }};"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">No year level data available.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
