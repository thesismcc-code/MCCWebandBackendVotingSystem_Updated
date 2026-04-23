@extends('components.admin-layout')
@section('title', 'Election Control — MCC Voting System')
@section('page-title', 'Election Control')
@section('page-sub', 'Manage the setup of the voting cycle')

@section('content')
<div x-data="{ activeModal: null }">

@if(session('success'))
<div class="mb-4 flex items-center justify-between bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl text-sm font-semibold" id="flash-success">
    <span>{{ session('success') }}</span>
    <button onclick="document.getElementById('flash-success').remove()" class="text-green-500 hover:text-green-700 ml-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center justify-between bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl text-sm font-semibold" id="flash-error">
    <span>{{ session('error') }}</span>
    <button onclick="document.getElementById('flash-error').remove()" class="text-red-400 hover:text-red-600 ml-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl text-sm">
    <p class="font-bold mb-1">Please fix the following errors:</p>
    <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif

{{-- Action Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div @click="activeModal = 'general'"
         class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center justify-between cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800 group-hover:text-[#1a5c38] transition-colors">General Settings</p>
                <p class="text-xs text-gray-400 mt-0.5">Name, semester, academic year</p>
            </div>
        </div>
        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2d7a52] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </div>

    <div @click="activeModal = 'schedule'"
         class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center justify-between cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#4CAF7D,#2d7a52);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800 group-hover:text-[#1a5c38] transition-colors">Schedule Settings</p>
                <p class="text-xs text-gray-400 mt-0.5">Dates, opening &amp; closing times</p>
            </div>
        </div>
        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2d7a52] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </div>

    <a href="{{ route('view.election-control-posistion-setup') }}"
       class="bg-white border border-gray-100 rounded-2xl shadow-sm px-5 py-4 flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 bg-amber-100">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800 group-hover:text-[#1a5c38] transition-colors">Position Setup</p>
                <p class="text-xs text-gray-400 mt-0.5">Configure election positions</p>
            </div>
        </div>
        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2d7a52] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>

{{-- Current Election --}}
@if($activeElection)
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-3">
            <h3 class="text-base font-bold text-gray-800">Current Election Settings</h3>
            @php
                $statusColor = match($scheduleStatus) { 'active' => 'bg-green-100 text-green-700 border-green-200', 'ready' => 'bg-yellow-100 text-yellow-700 border-yellow-200', 'upcoming' => 'bg-blue-100 text-blue-700 border-blue-200', 'overdue' => 'bg-red-100 text-red-700 border-red-200', default => 'bg-gray-100 text-gray-500 border-gray-200' };
                $statusLabel = match($scheduleStatus) { 'active' => '● Active', 'ready' => '● Ready to Start', 'upcoming' => '◷ Upcoming', 'overdue' => '⚠ Overdue', default => '○ Not Scheduled' };
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColor }}">{{ $statusLabel }}</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($canStart && $activeElectionId)
            <form method="POST" action="{{ route('election.update-status') }}">
                @csrf
                <input type="hidden" name="election_id" value="{{ $activeElectionId }}">
                <input type="hidden" name="status" value="active">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all" style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653Z"/></svg>
                    Start Election
                </button>
            </form>
            @endif
            @if($canClose && $activeElectionId)
            <form method="POST" action="{{ route('election.update-status') }}">
                @csrf
                <input type="hidden" name="election_id" value="{{ $activeElectionId }}">
                <input type="hidden" name="status" value="closed">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9Z"/></svg>
                    Close Election
                </button>
            </form>
            @endif
            <button @click="activeModal = 'general'" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit General
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
        <div class="bg-[#f4f6f0] rounded-xl p-4"><p class="text-xs text-gray-400 font-semibold mb-1">Election Name</p><p class="text-sm font-bold text-gray-800">{{ $activeElection['election_name'] ?? 'Not Set' }}</p></div>
        <div class="bg-[#f4f6f0] rounded-xl p-4"><p class="text-xs text-gray-400 font-semibold mb-1">Semester</p><p class="text-sm font-bold text-gray-800">{{ $activeElection['semester'] ?? 'Not Set' }}</p></div>
        <div class="bg-[#f4f6f0] rounded-xl p-4"><p class="text-xs text-gray-400 font-semibold mb-1">Academic Year</p><p class="text-sm font-bold text-gray-800">{{ $activeElection['academic_year'] ?? 'Not Set' }}</p></div>
    </div>

    @if(isset($activeElection['date_from']) || isset($activeElection['opening_time']))
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-bold text-gray-700">Schedule Information</h4>
        <button @click="activeModal = 'schedule'" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Schedule
        </button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-[#f4f6f0] rounded-xl p-4"><p class="text-xs text-gray-400 font-semibold mb-1">Date From</p><p class="text-sm font-bold text-gray-800">{{ isset($activeElection['date_from']) ? date('M d, Y', strtotime($activeElection['date_from'])) : 'Not Set' }}</p></div>
        <div class="bg-[#f4f6f0] rounded-xl p-4"><p class="text-xs text-gray-400 font-semibold mb-1">Date To</p><p class="text-sm font-bold text-gray-800">{{ isset($activeElection['date_to']) ? date('M d, Y', strtotime($activeElection['date_to'])) : 'Not Set' }}</p></div>
        <div class="bg-[#f4f6f0] rounded-xl p-4"><p class="text-xs text-gray-400 font-semibold mb-1">Opening Time</p><p class="text-sm font-bold text-gray-800">{{ isset($activeElection['opening_time']) ? date('h:i A', strtotime($activeElection['opening_time'])) : 'Not Set' }}</p></div>
        <div class="bg-[#f4f6f0] rounded-xl p-4"><p class="text-xs text-gray-400 font-semibold mb-1">Closing Time</p><p class="text-sm font-bold text-gray-800">{{ isset($activeElection['closing_time']) ? date('h:i A', strtotime($activeElection['closing_time'])) : 'Not Set' }}</p></div>
    </div>
    @php
        $autoNote = null;
        if (isset($activeElection['date_from'], $activeElection['opening_time'], $activeElection['date_to'], $activeElection['closing_time'])) {
            $startDt = \Carbon\Carbon::parse($activeElection['date_from'] . ' ' . $activeElection['opening_time']);
            $endDt   = \Carbon\Carbon::parse($activeElection['date_to']   . ' ' . $activeElection['closing_time']);
            $now     = \Carbon\Carbon::now();
            if ($now->lessThan($startDt)) { $autoNote = 'Election will automatically start on ' . $startDt->format('M d, Y \a\t h:i A'); }
            elseif ($now->between($startDt, $endDt) && ($activeElection['status'] ?? '') === 'active') { $autoNote = 'Election will automatically close on ' . $endDt->format('M d, Y \a\t h:i A'); }
        }
    @endphp
    @if($autoNote)
    <div class="mt-3 flex items-center gap-2 text-xs text-gray-500 bg-[#f4f6f0] rounded-xl px-4 py-2.5">
        <svg class="w-4 h-4 shrink-0 text-[#2d7a52]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z"/></svg>
        {{ $autoNote }}
    </div>
    @endif
    @endif
</div>
@else
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-10 text-center">
    <div class="text-4xl mb-3">🗳️</div>
    <p class="text-gray-500 font-semibold">No election configured yet.</p>
    <p class="text-gray-400 text-sm mt-1">Click <strong>General Settings</strong> to create one.</p>
</div>
@endif

{{-- Modals --}}
<div x-show="activeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4" role="dialog" aria-modal="true">
    <div x-show="activeModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="activeModal = null"></div>

    {{-- Schedule Modal --}}
    <div x-show="activeModal === 'schedule'" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative z-50">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-bold text-gray-900">Schedule Settings</h2>
            <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('election.save-schedule') }}" method="POST" class="space-y-4">
            @csrf
            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Date From</label><input type="date" name="date_from" value="{{ $activeElection['date_from'] ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" required></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Date To</label><input type="date" name="date_to" value="{{ $activeElection['date_to'] ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" required></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Opening Time</label><input type="time" name="opening_time" value="{{ $activeElection['opening_time'] ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" required></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Closing Time</label><input type="time" name="closing_time" value="{{ $activeElection['closing_time'] ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" required></div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="activeModal = null" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all" style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">Save</button>
            </div>
        </form>
    </div>

    {{-- General Modal --}}
    <div x-show="activeModal === 'general'" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative z-50">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-bold text-gray-900">General Settings</h2>
            <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('election.save-general') }}" method="POST" class="space-y-4">
            @csrf
            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Election Name</label><input type="text" name="election_name" value="{{ $activeElection['election_name'] ?? '' }}" placeholder="e.g., SSC General Election 2025" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" required></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label><select name="semester" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" required><option value="">Select Semester</option><option value="1st Semester" {{ ($activeElection['semester'] ?? '') == '1st Semester' ? 'selected' : '' }}>1st Semester</option><option value="2nd Semester" {{ ($activeElection['semester'] ?? '') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option></select></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Academic Year</label><input type="text" name="academic_year" value="{{ $activeElection['academic_year'] ?? '' }}" placeholder="e.g., 2025-2026" pattern="\d{4}-\d{4}" title="Format: YYYY-YYYY" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" required></div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="activeModal = null" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all" style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">Save</button>
            </div>
        </form>
    </div>
</div>

</div>
<script>
    setTimeout(() => { const el = document.getElementById('flash-success'); if (el) { el.style.transition = 'opacity .5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); } }, 5000);
</script>
@endsection
