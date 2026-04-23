@extends('components.admin-layout')
@section('title', 'Election History — MCC Voting System')
@section('page-title', 'Election History')
@section('page-sub', 'All past and current elections')

@section('content')
<style>
    .status-badge {
        display:inline-flex;align-items:center;gap:5px;
        font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;
    }
    .status-active   { background:#dcfce7;color:#15803d;border:1px solid rgba(21,128,61,.2); }
    .status-closed   { background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb; }
    .status-upcoming { background:#fef9c3;color:#92400e;border:1px solid rgba(251,191,36,.3); }
    .status-dot { width:6px;height:6px;border-radius:50%;display:inline-block; }
    .dot-active   { background:#22c55e;animation:pulse 1.4s infinite; }
    .dot-closed   { background:#9ca3af; }
    .dot-upcoming { background:#f59e0b; }
    @keyframes pulse { 0%,100%{opacity:1}50%{opacity:.4} }
</style>

{{-- Summary Stats --}}
@php
    $total   = count($elections);
    $active  = collect($elections)->where('status','active')->count();
    $closed  = collect($elections)->where('status','closed')->count();
    $totalVoters = collect($elections)->sum('voters');
@endphp
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="text-3xl font-extrabold text-gray-900">{{ $total }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide mt-1">Total Elections</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="text-3xl font-extrabold text-green-600">{{ $active }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide mt-1">Currently Active</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="text-3xl font-extrabold text-gray-500">{{ $closed }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide mt-1">Completed</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="text-3xl font-extrabold text-gray-900">{{ number_format($totalVoters) }}</div>
        <div class="text-xs font-700 text-gray-400 uppercase tracking-wide mt-1">Total Voters Across All</div>
    </div>
</div>

{{-- Elections Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between" style="background:#f8faf6;">
        <h3 class="font-extrabold text-sm text-gray-700">All Elections</h3>
        <span class="text-xs text-gray-400 font-medium">{{ $total }} record{{ $total !== 1 ? 's' : '' }}</span>
    </div>

    @if(empty($elections))
    <div class="text-center py-16">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-gray-400 text-sm font-medium">No elections found.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr style="background:#1a5c38;">
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Election</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Period</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-center">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-center">Candidates</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-center">Voters</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-center">Votes Cast</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($elections as $election)
                @php
                    $statusClass = match($election['status']) {
                        'active'   => 'status-active',
                        'closed'   => 'status-closed',
                        'upcoming' => 'status-upcoming',
                        default    => 'status-closed',
                    };
                    $dotClass = match($election['status']) {
                        'active'   => 'dot-active',
                        'upcoming' => 'dot-upcoming',
                        default    => 'dot-closed',
                    };
                @endphp
                <tr class="hover:bg-green-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 text-sm">{{ $election['name'] }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $election['semester'] }} {{ $election['academic_year'] }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($election['date_from'] && $election['date_to'])
                        <div class="text-sm font-medium text-gray-700">
                            {{ \Carbon\Carbon::parse($election['date_from'])->format('M d, Y') }}
                            — {{ \Carbon\Carbon::parse($election['date_to'])->format('M d, Y') }}
                        </div>
                        @if($election['opening_time'] && $election['closing_time'])
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ \Carbon\Carbon::parse($election['opening_time'])->format('g:i A') }}
                            – {{ \Carbon\Carbon::parse($election['closing_time'])->format('g:i A') }}
                        </div>
                        @endif
                        @else
                        <span class="text-xs text-gray-400">Not scheduled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="status-badge {{ $statusClass }}">
                            <span class="status-dot {{ $dotClass }}"></span>
                            {{ ucfirst($election['status']) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-gray-700">{{ $election['candidates'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-gray-700">{{ number_format($election['voters']) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold" style="color:#1a5c38;">{{ number_format($election['votes_cast']) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if(in_array($election['status'], ['active', 'closed']))
                        <a href="{{ route('view.reports-and-analytics-end-of-election') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white transition-all hover:-translate-y-0.5"
                           style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Report
                        </a>
                        @else
                        <span class="text-xs text-gray-300 font-medium">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
