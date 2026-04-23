@extends('components.comelec-layout')
@section('title', 'Manage Candidates — MCC Voting System')
@section('page-title', 'Manage Candidates')
@section('page-sub', '{{ $activeElectionName ?? "No active election" }}')

@section('content')

<div x-data="{
    activePosition: null,
    showAdd: false,
    showDeleteConfirm: false,
    deleteId: null,
    deleteName: '',
    studentSearch: '',
    get filteredStudents() {
        if (!this.studentSearch) return window._students || [];
        const q = this.studentSearch.toLowerCase();
        return (window._students || []).filter(s => s.name.toLowerCase().includes(q));
    }
}">

@php $totalCandidates = array_sum(array_map('count', $byPosition)); @endphp

{{-- Alerts --}}
@if(session('success'))
<div class="flex items-center gap-3 mb-5 px-4 py-3 rounded-xl text-sm font-semibold" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#15803d;">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 mb-5 px-4 py-3 rounded-xl text-sm font-semibold" style="background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626;">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- Header row --}}
<div class="flex items-center justify-between mb-5">
    <div>
        @if($activeElectionName)
            <span class="inline-flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full" style="background:#e8f5ee;color:#1a5c38;">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>
                {{ $activeElectionName }}
            </span>
        @else
            <span class="inline-flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full" style="background:#fee2e2;color:#dc2626;">
                No active election
            </span>
        @endif
    </div>
    @if($activeElectionId)
    <button @click="showAdd = true"
        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg"
        style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 12px rgba(26,92,56,.3);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Add Candidate
    </button>
    @endif
</div>

@if(!$activeElectionId)
    <div class="text-center py-16">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-gray-400 text-sm font-semibold">No active election</p>
        <p class="text-gray-300 text-xs mt-1">Candidates can only be managed during an active election.</p>
    </div>
@elseif(empty($byPosition))
    <div class="text-center py-16">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="text-gray-400 text-sm font-semibold">No candidates yet</p>
        <p class="text-gray-300 text-xs mt-1">Click "Add Candidate" to get started.</p>
    </div>
@else
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden">
            <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#1a5c38,#2d7a52,#4ade80);"></div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 12px rgba(26,92,56,.3);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-gray-900 leading-none">{{ $totalCandidates }}</div>
                <div class="text-xs font-700 text-gray-400 uppercase tracking-wide mt-1">Total Candidates</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden">
            <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:linear-gradient(90deg,#0d9488,#2dd4bf);"></div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#0d9488,#0f766e);box-shadow:0 4px 12px rgba(13,148,136,.3);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-gray-900 leading-none">{{ count($byPosition) }}</div>
                <div class="text-xs font-700 text-gray-400 uppercase tracking-wide mt-1">Positions</div>
            </div>
        </div>
    </div>

    {{-- Position Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($byPosition as $position => $candidates)
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col cursor-pointer hover:-translate-y-1 hover:shadow-lg transition-all duration-200 group"
             @click="activePosition = {{ json_encode(['position' => $position, 'candidates' => $candidates]) }}">

            <div class="px-5 py-4 flex items-center justify-between group-hover:opacity-90 transition-opacity" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                <div>
                    <span class="text-[10px] font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,.6);">Position</span>
                    <h3 class="text-white font-extrabold text-[16px] leading-tight mt-0.5">{{ strtoupper($position) }}</h3>
                </div>
                <div class="rounded-full px-3 py-1 text-white text-xs font-bold" style="background:rgba(255,255,255,.2);">
                    {{ count($candidates) }}
                </div>
            </div>

            <div class="flex-1 divide-y divide-gray-50">
                @foreach($candidates as $c)
                <div class="flex items-center gap-3 px-4 py-3">
                    @if(!empty($c['photo']))
                        <img src="{{ $c['photo'] }}" class="w-9 h-9 rounded-full object-cover border-2 border-gray-100 flex-shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                            {{ strtoupper(substr($c['name'], 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 text-[13px] truncate">{{ $c['name'] }}</div>
                        <div class="text-[11px] text-gray-400 truncate">{{ $c['course'] }} · {{ $c['year_level'] }}</div>
                    </div>
                    <span class="flex-shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full
                        {{ $c['status'] === 'approved' ? '' : ($c['status'] === 'rejected' ? '' : '') }}"
                        style="{{ $c['status'] === 'approved' ? 'background:#dcfce7;color:#15803d;' : ($c['status'] === 'rejected' ? 'background:#fee2e2;color:#dc2626;' : 'background:#fef9c3;color:#92400e;') }}">
                        {{ ucfirst($c['status']) }}
                    </span>
                </div>
                @endforeach
            </div>

            <div class="px-4 py-2.5 border-t border-gray-100 flex items-center justify-center gap-1.5 text-[11px] font-medium transition-colors" style="background:#f8faf6;color:#9ab09a;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Manage
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- ── POSITION DETAIL MODAL ── --}}
<div x-cloak x-show="activePosition"
     class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
     style="background:rgba(0,0,0,.55);backdrop-filter:blur(4px);"
     @click.self="activePosition = null"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

        <div class="px-6 py-5 flex items-center justify-between" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,.6);">Position</p>
                <h2 class="text-white font-extrabold text-xl mt-0.5" x-text="activePosition?.position?.toUpperCase()"></h2>
            </div>
            <button @click="activePosition = null" class="w-8 h-8 rounded-full flex items-center justify-center text-white transition-colors" style="background:rgba(255,255,255,.2);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="sticky top-0" style="background:#f8faf6;border-bottom:1px solid #e8ede3;">
                    <tr>
                        <th class="pl-6 pr-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wide">Candidate</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wide">Course</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wide">Year</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wide">Status</th>
                        <th class="pr-6 py-3 text-right font-bold text-gray-700 text-xs uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="c in activePosition?.candidates ?? []" :key="c.id">
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="pl-6 pr-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <template x-if="c.photo">
                                        <img :src="c.photo" class="w-9 h-9 rounded-full object-cover border-2 border-gray-100 flex-shrink-0">
                                    </template>
                                    <template x-if="!c.photo">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                             style="background:linear-gradient(135deg,#1a5c38,#2d7a52);"
                                             x-text="c.name?.charAt(0)?.toUpperCase()"></div>
                                    </template>
                                    <span class="font-semibold text-gray-900" x-text="c.name"></span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 text-sm" x-text="c.course"></td>
                            <td class="px-4 py-3.5 text-gray-600 text-sm" x-text="c.year_level"></td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full"
                                      :style="c.status === 'approved' ? 'background:#dcfce7;color:#15803d;' : c.status === 'rejected' ? 'background:#fee2e2;color:#dc2626;' : 'background:#fef9c3;color:#92400e;'"
                                      x-text="c.status?.charAt(0).toUpperCase() + c.status?.slice(1)"></span>
                            </td>
                            <td class="pr-6 py-3.5">
                                <div class="flex items-center gap-2 justify-end">
                                    {{-- Approve --}}
                                    <form method="POST" action="{{ route('comelec.candidate.status') }}">
                                        @csrf
                                        <input type="hidden" name="candidate_id" :value="c.id">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" title="Approve"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:-translate-y-0.5"
                                            style="background:#dcfce7;color:#15803d;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        </button>
                                    </form>
                                    {{-- Reject --}}
                                    <form method="POST" action="{{ route('comelec.candidate.status') }}">
                                        @csrf
                                        <input type="hidden" name="candidate_id" :value="c.id">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" title="Reject"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:-translate-y-0.5"
                                            style="background:#fef9c3;color:#d97706;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                    {{-- Delete --}}
                                    <button type="button" title="Remove"
                                        @click="deleteId = c.id; deleteName = c.name; showDeleteConfirm = true; activePosition = null"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:-translate-y-0.5"
                                        style="background:#fee2e2;color:#dc2626;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── ADD CANDIDATE MODAL ── --}}
<div x-cloak x-show="showAdd"
     class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
     style="background:rgba(0,0,0,.55);backdrop-filter:blur(4px);"
     @click.self="showAdd = false"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

        <div class="px-6 py-5 flex items-center justify-between" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <h2 class="text-white font-extrabold text-lg">Add Candidate</h2>
            <button @click="showAdd = false" class="w-8 h-8 rounded-full flex items-center justify-center text-white" style="background:rgba(255,255,255,.2);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('comelec.candidate.add') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Student</label>
                <input type="text" x-model="studentSearch" placeholder="Search student name..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:border-transparent mb-2"
                    style="focus:ring-color:#1a5c38;">
                <select name="student_id" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 bg-white appearance-none">
                    <option value="">— Select student —</option>
                    <template x-for="s in filteredStudents" :key="s.id">
                        <option :value="s.id" x-text="s.name + (s.course ? ' · ' + s.course : '') + (s.year_level ? ' · ' + s.year_level : '')"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Position</label>
                <select name="position" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 bg-white appearance-none">
                    <option value="">— Select position —</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos['name'] }}">{{ $pos['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Photo <span class="text-gray-300 font-normal normal-case">(optional)</span></label>
                <input type="file" name="image" accept="image/*"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:font-semibold"
                    style="file:background:#e8f5ee;file:color:#1a5c38;">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="showAdd = false"
                    class="flex-1 py-2.5 rounded-xl border-2 border-gray-200 text-gray-500 font-bold text-sm hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-white font-bold text-sm transition-all hover:-translate-y-0.5"
                    style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 12px rgba(26,92,56,.3);">
                    Add Candidate
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── DELETE CONFIRM MODAL ── --}}
<div x-cloak x-show="showDeleteConfirm"
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background:rgba(0,0,0,.55);backdrop-filter:blur(4px);"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:linear-gradient(135deg,#fee2e2,#fecaca);">
            <svg class="w-8 h-8" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-2">Remove Candidate?</h3>
        <p class="text-sm text-gray-500 font-medium mb-6">
            Are you sure you want to remove <strong x-text="deleteName" class="text-gray-900"></strong> from the candidates list?
        </p>
        <div class="flex gap-3">
            <button @click="showDeleteConfirm = false; deleteId = null"
                class="flex-1 py-2.5 rounded-xl border-2 border-gray-200 text-gray-500 font-bold text-sm hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <form method="POST" :action="'/comelec-candidates/' + deleteId" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full py-2.5 rounded-xl text-white font-bold text-sm transition-all hover:-translate-y-0.5"
                    style="background:linear-gradient(135deg,#b91c1c,#dc2626);box-shadow:0 4px 12px rgba(185,28,28,.3);">
                    Remove
                </button>
            </form>
        </div>
    </div>
</div>

</div>

<script>window._students = @json($students);</script>
@endsection
