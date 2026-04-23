@extends('components.admin-layout')
@section('title', 'Voting Logs — MCC Voting System')
@section('page-title', 'Voting Logs')
@section('page-sub', 'Track every vote cast in the election')

@section('content')

{{-- Action Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <a href="{{ route('view.security-logs') }}"
       class="flex items-center gap-4 bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-800 group-hover:text-[#1a5c38] transition-colors">Security Logs</p>
            <p class="text-xs text-gray-400 mt-0.5">Monitor suspicious activity</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2d7a52] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
    <a href="{{ route('voting-logs.export-pdf') }}" target="_blank"
       class="flex items-center gap-4 bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#4CAF7D,#2d7a52);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-800 group-hover:text-[#1a5c38] transition-colors">Export PDF</p>
            <p class="text-xs text-gray-400 mt-0.5">Download voting records</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2d7a52] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('view.voting-logs') }}" class="flex flex-wrap gap-3 mb-5">
    <div class="relative flex-1 min-w-56">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Student ID or Name"
               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-700 placeholder-gray-400">
    </div>
    <select name="course" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-600 min-w-40">
        <option value="">All Courses</option>
        <option value="Computer Science" {{ request('course') === 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
        <option value="Information Technology" {{ request('course') === 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
        <option value="Business Administration" {{ request('course') === 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
    </select>
    <select name="year_level" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-600 min-w-36">
        <option value="">Year Level</option>
        <option value="1st Year" {{ request('year_level') === '1st Year' ? 'selected' : '' }}>1st Year</option>
        <option value="2nd Year" {{ request('year_level') === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
        <option value="3rd Year" {{ request('year_level') === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
        <option value="4th Year" {{ request('year_level') === '4th Year' ? 'selected' : '' }}>4th Year</option>
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
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Date &amp; Time</th>
                <th class="px-5 py-3.5 text-center text-[11px] font-bold text-white uppercase tracking-widest">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($logs as $log)
            <tr class="hover:bg-green-50/30 transition-colors">
                <td class="px-5 py-3.5 font-bold text-[13px]" style="color:#1a5c38;">{{ $log['student_id'] }}</td>
                <td class="px-5 py-3.5 font-semibold text-gray-800 text-[13px]">{{ $log['name'] }}</td>
                <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $log['course'] ?? '—' }}</td>
                <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $log['year_level'] }}</td>
                <td class="px-5 py-3.5 text-gray-400 text-[13px]">{{ $log['voted_at'] ? \Carbon\Carbon::parse($log['voted_at'])->format('M d, Y g:iA') : '—' }}</td>
                <td class="px-5 py-3.5 text-center">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Voted
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-14 text-center"><p class="text-sm font-medium text-gray-300">No voting records found.</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $logs->hasPages())
<div class="flex justify-center mt-5">{{ $logs->links() }}</div>
@endif

@endsection
