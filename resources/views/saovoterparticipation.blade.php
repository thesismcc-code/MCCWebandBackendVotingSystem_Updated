@extends('components.sao-layout')
@section('title', 'Voter Participation — MCC Voting System')
@section('page-title', 'Voter Participation')
@section('page-sub', '{{ $activeElectionName }}')

@section('content')

{{-- Filters --}}
<form id="filterForm" action="{{ request()->url() }}" method="GET" class="flex flex-wrap gap-3 mb-5">
    {{-- Search --}}
    <div class="relative flex-1 min-w-[220px]">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#9ab09a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
        <input type="text" id="searchInput" name="search"
            value="{{ $search }}"
            placeholder="Search by Student ID or Name"
            autocomplete="off"
            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:border-transparent transition-all"
            style="focus:ring-color:#1a5c38;">
    </div>

    {{-- Course Filter --}}
    <select name="course_filter" onchange="this.form.submit()"
        class="pl-4 pr-8 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-600 focus:outline-none focus:ring-2 appearance-none cursor-pointer">
        <option value="">All Courses</option>
        @foreach($courseOptions as $c)
            <option value="{{ $c }}" {{ $courseFilter === $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
    </select>

    {{-- Year Level Filter --}}
    <select name="year_filter" onchange="this.form.submit()"
        class="pl-4 pr-8 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-600 focus:outline-none focus:ring-2 appearance-none cursor-pointer">
        <option value="">All Year Levels</option>
        @foreach([1=>'1st Year',2=>'2nd Year',3=>'3rd Year',4=>'4th Year'] as $val => $label)
            <option value="{{ $val }}" {{ $yearFilter == $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>

    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold" style="background:#e8f5ee;color:#1a5c38;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        {{ $totalVoters }} {{ $totalVoters === 1 ? 'voter' : 'voters' }}
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr style="background:#1a5c38;">
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Student ID</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Name</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Course</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Year Level</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Date &amp; Time</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($voters as $voter)
                <tr class="hover:bg-green-50/30 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $voter->student_id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                                {{ strtoupper(substr($voter->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $voter->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $voter->course }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $voter->year_level }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($voter->voted_at)
                            {{ \Carbon\Carbon::parse($voter->voted_at)->format('d-m-Y g:iA') }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1.5 rounded-full" style="background:#dcfce7;color:#15803d;">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                            Voted
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="text-center py-16">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            @if($activeElectionName === 'No Active Election')
                                <p class="text-gray-400 text-sm font-semibold">No active election found.</p>
                            @elseif($search || $courseFilter || $yearFilter)
                                <p class="text-gray-400 text-sm font-semibold">No voters match your filters.</p>
                                <p class="text-gray-300 text-xs mt-1">Try adjusting your search or filters.</p>
                            @else
                                <p class="text-gray-400 text-sm font-semibold">No votes have been cast yet.</p>
                                <p class="text-gray-300 text-xs mt-1">Votes will appear here once students start voting.</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($voters->hasPages())
<div class="flex items-center justify-between mt-5 px-1">
    <p class="text-sm text-gray-400 font-medium">
        Showing {{ $voters->firstItem() }}–{{ $voters->lastItem() }} of {{ $voters->total() }} voters
    </p>
    <div class="flex items-center gap-2">
        @if($voters->onFirstPage())
            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $voters->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:text-white transition-all" style="hover:background:#1a5c38;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        @foreach($voters->getUrlRange(1, $voters->lastPage()) as $page => $url)
            @if($page == $voters->currentPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-white text-sm font-bold" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-sm font-medium hover:bg-gray-200 transition-all">{{ $page }}</a>
            @endif
        @endforeach

        @if($voters->hasMorePages())
            <a href="{{ $voters->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </div>
</div>
@endif

<script>
    let searchTimer;
    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { document.getElementById('filterForm').submit(); }, 400);
    });
</script>
@endsection
