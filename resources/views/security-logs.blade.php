@extends('components.admin-layout')
@section('title', 'Security Logs — MCC Voting System')
@section('page-title', 'Security Logs')
@section('page-sub', 'Monitor suspicious and failed authentication attempts')

@section('content')

<div x-data="{ searchQuery: '{{ $searchQuery }}', courseFilter: '{{ $courseFilter }}', yearLevelFilter: '{{ $yearLevelFilter }}' }">

{{-- Stat Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['duplicate_vote_attempts'] }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-0.5">Duplicate Vote Attempts</p>
        </div>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-red-100">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['rejected_fingerprint_scans'] }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-0.5">Rejected Fingerprint Scans</p>
        </div>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-amber-100">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['denied_access_attempts'] }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-0.5">Denied Access Attempts</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<form id="filter-form" method="GET" action="{{ route('view.security-logs') }}" class="flex flex-wrap gap-3 mb-5">
    <div class="relative flex-1 min-w-56">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" x-model="searchQuery" @input.debounce.500ms="$el.form.submit()" value="{{ $searchQuery }}"
               placeholder="Search by Student ID or Name"
               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-700 placeholder-gray-400">
    </div>
    <select name="course" x-model="courseFilter" @change="$el.form.submit()"
            class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-600 min-w-44">
        <option value="">All Courses</option>
        @foreach($courses as $course)
            <option value="{{ $course }}" {{ $courseFilter === $course ? 'selected' : '' }}>{{ $course }}</option>
        @endforeach
    </select>
    <select name="year_level" x-model="yearLevelFilter" @change="$el.form.submit()"
            class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-600 min-w-36">
        <option value="">Year Level</option>
        @foreach($yearLevels as $yearLevel)
            <option value="{{ $yearLevel }}" {{ $yearLevelFilter === $yearLevel ? 'selected' : '' }}>{{ $yearLevel }}</option>
        @endforeach
    </select>
</form>

{{-- Table --}}
<div class="rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#1a5c38;">
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Student ID</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Name</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Course</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Year Level</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">First Attempt</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Second Attempt</th>
                    <th class="px-5 py-3.5 text-center text-[11px] font-bold text-white uppercase tracking-widest">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($logs as $log)
                <tr class="hover:bg-green-50/30 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-[13px]" style="color:#1a5c38;">{{ $log['student_id'] }}</td>
                    <td class="px-5 py-3.5 font-semibold text-gray-800 text-[13px]">{{ $log['name'] }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $log['course'] }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $log['year_level'] }}</td>
                    <td class="px-5 py-3.5 text-gray-400 text-[13px]">{{ $log['first_attempt'] ? \Carbon\Carbon::parse($log['first_attempt'])->format('M d, Y g:iA') : '—' }}</td>
                    <td class="px-5 py-3.5 text-gray-400 text-[13px]">{{ $log['second_attempt'] ? \Carbon\Carbon::parse($log['second_attempt'])->format('M d, Y g:iA') : '—' }}</td>
                    <td class="px-5 py-3.5 text-center">
                        @if($log['status'] === 'Blocked')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Blocked</span>
                        @elseif($log['status'] === 'Rejected')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Rejected</span>
                        @elseif($log['status'] === 'Denied')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Denied</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">{{ $log['status'] }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-14 text-center"><p class="text-sm font-medium text-gray-300">No security incidents found.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
