@extends('components.admin-layout')
@section('title', 'System Activity — MCC Voting System')
@section('page-title', 'System Activity')
@section('page-sub', 'Real-time monitoring of system usage and security events')

@section('content')

{{-- Toggle Buttons --}}
<div class="flex gap-2 mb-5">
    <button id="btnRealTime" onclick="showRealTime()"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm text-white"
            style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">
        Real Time Logs
    </button>
    <button id="btnError" onclick="showError()"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all border border-gray-200 bg-white text-gray-600 hover:bg-gray-50">
        Error Logs
    </button>
</div>

{{-- Filter Row --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
    <h2 id="tableTitle" class="text-lg font-bold text-gray-800">Real Time Logs</h2>
    <div class="flex gap-3" x-data>
        {{-- User Filter --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors min-w-36">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="flex-1 text-left">{{ $currentUserFilter === 'all' ? 'All Users' : ucfirst($currentUserFilter) }}</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" x-transition x-cloak class="absolute right-0 mt-1.5 w-44 bg-white border border-gray-100 rounded-xl shadow-lg py-1.5 z-30 text-sm">
                <li><a href="?user=all&date={{ $currentDateFilter }}" class="block px-4 py-2 font-medium text-gray-700 hover:bg-green-50 hover:text-[#1a5c38] transition-colors {{ $currentUserFilter === 'all' ? 'text-[#1a5c38] font-bold' : '' }}">All Users</a></li>
                @foreach($users as $user)
                <li><a href="?user={{ strtolower($user) }}&date={{ $currentDateFilter }}" class="block px-4 py-2 font-medium text-gray-700 hover:bg-green-50 hover:text-[#1a5c38] transition-colors {{ strtolower($currentUserFilter) === strtolower($user) ? 'text-[#1a5c38] font-bold' : '' }}">{{ $user }}</a></li>
                @endforeach
            </ul>
        </div>
        {{-- Date Filter --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors min-w-36">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="flex-1 text-left">@if($currentDateFilter === 'all') All Dates @elseif($currentDateFilter === 'today') Today @elseif($currentDateFilter === 'yesterday') Yesterday @elseif($currentDateFilter === 'last_week') Last Week @elseif($currentDateFilter === 'last_month') Last Month @else All Dates @endif</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" x-transition x-cloak class="absolute right-0 mt-1.5 w-44 bg-white border border-gray-100 rounded-xl shadow-lg py-1.5 z-30 text-sm">
                @foreach(['all' => 'All Dates', 'today' => 'Today', 'yesterday' => 'Yesterday', 'last_week' => 'Last Week', 'last_month' => 'Last Month'] as $val => $label)
                <li><a href="?user={{ $currentUserFilter }}&date={{ $val }}" class="block px-4 py-2 font-medium text-gray-700 hover:bg-green-50 hover:text-[#1a5c38] transition-colors {{ $currentDateFilter === $val ? 'text-[#1a5c38] font-bold' : '' }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@php
    $tableHead = '<thead><tr style="background:#1a5c38;"><th class="px-5 py-3.5 text-center text-[11px] font-bold text-white uppercase tracking-widest w-36">Date</th><th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest w-36">Time</th><th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest w-36">User</th><th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Activity</th></tr></thead>';
@endphp

{{-- Real Time Logs --}}
<div id="realTimeTable" class="rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
        <thead><tr style="background:#1a5c38;">
            <th class="px-5 py-3.5 text-center text-[11px] font-bold text-white uppercase tracking-widest w-36">Date</th>
            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest w-36">Time</th>
            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest w-36">User</th>
            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Activity</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($realTimeLogs as $log)
                @php try { $ts = \Carbon\Carbon::parse($log['created_at'] ?? now()); $date = $ts->format('M d, Y'); $time = $ts->format('h:i:s A'); } catch(\Exception $e) { $date = 'N/A'; $time = 'N/A'; } @endphp
                <tr class="hover:bg-green-50/30 transition-colors">
                    <td class="px-5 py-3.5 text-center text-gray-500 text-[13px]">{{ $date }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $time }}</td>
                    <td class="px-5 py-3.5 font-semibold text-[13px]" style="color:#1a5c38;">{{ $log['user'] ?? 'Unknown' }}</td>
                    <td class="px-5 py-3.5 text-gray-700 text-[13px]">{{ $log['message'] ?? 'No message' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-14 text-center"><p class="text-sm font-medium text-gray-300">No real-time activity logs match your filters.</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Error Logs --}}
<div id="errorTable" class="rounded-2xl border border-gray-100 overflow-hidden shadow-sm hidden">
    <table class="w-full text-sm">
        <thead><tr style="background:#1a5c38;">
            <th class="px-5 py-3.5 text-center text-[11px] font-bold text-white uppercase tracking-widest w-36">Date</th>
            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest w-36">Time</th>
            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest w-36">User</th>
            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Activity</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($errorLogs as $log)
                @php try { $ts = \Carbon\Carbon::parse($log['created_at'] ?? now()); $date = $ts->format('M d, Y'); $time = $ts->format('h:i:s A'); } catch(\Exception $e) { $date = 'N/A'; $time = 'N/A'; } @endphp
                <tr class="hover:bg-red-50/30 transition-colors">
                    <td class="px-5 py-3.5 text-center text-gray-500 text-[13px]">{{ $date }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $time }}</td>
                    <td class="px-5 py-3.5 font-semibold text-[13px]" style="color:#1a5c38;">{{ $log['user'] ?? 'Unknown' }}</td>
                    <td class="px-5 py-3.5 text-red-600 text-[13px] font-medium">{{ $log['message'] ?? 'No message' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-14 text-center"><p class="text-sm font-medium text-gray-300">No error logs match your filters.</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function showRealTime() {
        document.getElementById('btnRealTime').style.cssText = 'background:linear-gradient(135deg,#2d7a52,#1a5c38);color:white;';
        document.getElementById('btnError').style.cssText = '';
        document.getElementById('btnError').className = 'px-5 py-2.5 rounded-xl text-sm font-bold transition-all border border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        document.getElementById('realTimeTable').classList.remove('hidden');
        document.getElementById('errorTable').classList.add('hidden');
        document.getElementById('tableTitle').innerText = 'Real Time Logs';
    }
    function showError() {
        document.getElementById('btnError').style.cssText = 'background:linear-gradient(135deg,#2d7a52,#1a5c38);color:white;';
        document.getElementById('btnRealTime').style.cssText = '';
        document.getElementById('btnRealTime').className = 'px-5 py-2.5 rounded-xl text-sm font-bold transition-all border border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        document.getElementById('errorTable').classList.remove('hidden');
        document.getElementById('realTimeTable').classList.add('hidden');
        document.getElementById('tableTitle').innerText = 'Error Logs';
    }
</script>

@endsection
