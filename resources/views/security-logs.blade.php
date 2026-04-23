@extends('components.admin-layout')

@section('title', 'Security Logs')
@section('page-title', 'Security Logs')
@section('page-sub', 'Monitor suspicious activity and security events')

@section('content')
<div>
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#fee2e2,#fecaca);">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-gray-900">{{ $stats['duplicate_vote_attempts'] }}</p>
                <p class="text-xs font-semibold text-gray-500 mt-0.5">Duplicate Vote Attempts</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-gray-900">{{ $stats['rejected_fingerprint_scans'] }}</p>
                <p class="text-xs font-semibold text-gray-500 mt-0.5">Rejected Fingerprint Scans</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-gray-900">{{ $stats['denied_access_attempts'] }}</p>
                <p class="text-xs font-semibold text-gray-500 mt-0.5">Denied Access Attempts</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('view.security-logs') }}" class="flex flex-wrap gap-3 mb-5">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Search by Student ID or Name"
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-700 placeholder-gray-400">
        </div>
        <select name="course" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-600 min-w-40">
            <option value="">All Courses</option>
            @foreach($courses as $course)
                <option value="{{ $course }}" {{ $courseFilter === $course ? 'selected' : '' }}>{{ $course }}</option>
            @endforeach
        </select>
        <select name="year_level" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-600 min-w-36">
            <option value="">Year Level</option>
            @foreach($yearLevels as $yl)
                <option value="{{ $yl }}" {{ $yearLevelFilter === $yl ? 'selected' : '' }}>{{ $yl }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all" style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">Search</button>
    </form>

    {{-- Table --}}
    <div class="rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#1a5c38;">
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Student ID</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Name</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Course</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Year Level</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Event Type</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Description</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Timestamp</th>
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
                    <td class="px-5 py-3.5 text-[13px]">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($log['type'] === 'duplicate_vote_attempt') bg-red-100 text-red-700
                            @elseif($log['type'] === 'rejected_fingerprint') bg-yellow-100 text-yellow-700
                            @else bg-purple-100 text-purple-700 @endif">
                            {{ ucwords(str_replace('_', ' ', $log['type'])) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $log['description'] ?: '—' }}</td>
                    <td class="px-5 py-3.5 text-gray-400 text-[13px]">
                        {{ $log['created_at'] ? \Carbon\Carbon::parse($log['created_at'])->format('M d, Y g:iA') : '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold
                            @if($log['status'] === 'Blocked') bg-red-100 text-red-700
                            @elseif($log['status'] === 'Rejected') bg-yellow-100 text-yellow-700
                            @else bg-purple-100 text-purple-700 @endif">
                            {{ $log['status'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No security events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
