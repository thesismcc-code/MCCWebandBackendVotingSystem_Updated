@extends('components.sao-layout')
@section('title', 'Candidate List — MCC Voting System')
@section('page-title', 'Candidate List')
@section('page-sub', 'Approved candidates for the active election')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ modal: false, selected: null }">

@if(empty($byPosition))
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background:#e8f5ee;">
            <svg class="w-8 h-8" fill="none" stroke="#2d7a52" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
        </div>
        <p class="text-gray-500 text-base font-semibold">No approved candidates found</p>
        <p class="text-gray-400 text-sm mt-1">Candidates will appear here once approved for the active election.</p>
    </div>
@else
    @php
        $totalCandidates = array_sum(array_map('count', $byPosition));
        $totalPositions  = count($byPosition);
    @endphp

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
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
                <div class="text-3xl font-extrabold text-gray-900 leading-none">{{ $totalPositions }}</div>
                <div class="text-xs font-700 text-gray-400 uppercase tracking-wide mt-1">Positions</div>
            </div>
        </div>
    </div>

    {{-- Positions Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($byPosition as $position => $candidates)
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col cursor-pointer hover:-translate-y-1 hover:shadow-lg transition-all duration-200 group"
             @click="selected = {{ json_encode(['position' => $position, 'candidates' => $candidates]) }}; modal = true">

            {{-- Position Header --}}
            <div class="px-5 py-4 flex items-center justify-between group-hover:opacity-90 transition-opacity" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                <div>
                    <span class="text-[10px] font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,.6);">Position</span>
                    <h3 class="text-white font-extrabold text-[16px] leading-tight mt-0.5">{{ strtoupper($position) }}</h3>
                </div>
                <div class="rounded-full px-3 py-1 text-white text-xs font-bold" style="background:rgba(255,255,255,.2);">
                    {{ count($candidates) }} {{ count($candidates) === 1 ? 'candidate' : 'candidates' }}
                </div>
            </div>

            {{-- Candidates preview --}}
            <div class="flex-1 divide-y divide-gray-50">
                @foreach($candidates as $i => $candidate)
                <div class="flex items-center gap-3.5 px-5 py-3.5">
                    @if(!empty($candidate['photo']))
                        <img src="{{ $candidate['photo'] }}" alt="{{ $candidate['name'] }}"
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-100 flex-shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                            {{ strtoupper(substr($candidate['name'], 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-gray-900 text-[14px] truncate">{{ $candidate['name'] }}</div>
                        <div class="flex flex-wrap gap-x-2 mt-0.5">
                            @if(!empty($candidate['course']))
                                <span class="text-[11.5px] text-gray-500">{{ $candidate['course'] }}</span>
                            @endif
                            @if(!empty($candidate['year_level']))
                                <span class="text-[11.5px] text-gray-400">· {{ $candidate['year_level'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold" style="background:#e8f5ee;color:#1a5c38;">
                        {{ $i + 1 }}
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Click hint --}}
            <div class="px-5 py-2.5 border-t border-gray-100 flex items-center justify-center gap-1.5 text-[11.5px] font-medium transition-colors" style="background:#f8faf6;color:#9ab09a;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                View details
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- Modal --}}
<div x-cloak x-show="modal"
     class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
     style="background:rgba(0,0,0,.55);backdrop-filter:blur(4px);"
     @click.self="modal = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="px-6 py-5 flex items-center justify-between" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,.6);">Position</p>
                <h2 class="text-white font-extrabold text-xl leading-tight mt-0.5" x-text="selected?.position?.toUpperCase()"></h2>
            </div>
            <button @click="modal = false"
                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors text-white" style="background:rgba(255,255,255,.2);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 pt-4 pb-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest"
               x-text="(selected?.candidates?.length ?? 0) + ' Candidate' + (selected?.candidates?.length === 1 ? '' : 's')"></p>
        </div>

        <div class="px-6 pb-6 space-y-3 max-h-[60vh] overflow-y-auto">
            <template x-for="(c, i) in selected?.candidates ?? []" :key="i">
                <div class="flex items-center gap-4 rounded-xl p-4 border border-gray-100" style="background:#f8faf6;">
                    <div class="flex-shrink-0">
                        <template x-if="c.photo">
                            <img :src="c.photo" :alt="c.name" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-sm">
                        </template>
                        <template x-if="!c.photo">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-sm"
                                 style="background:linear-gradient(135deg,#1a5c38,#2d7a52);"
                                 x-text="c.name?.charAt(0)?.toUpperCase()"></div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-gray-900 text-[15px] leading-snug" x-text="c.name"></div>
                        <div class="mt-1 space-y-0.5">
                            <template x-if="c.course">
                                <div class="text-[12px] text-gray-500" x-text="c.course"></div>
                            </template>
                            <template x-if="c.year_level">
                                <div class="text-[12px] text-gray-400" x-text="c.year_level"></div>
                            </template>
                            <template x-if="c.party_list">
                                <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full mt-1" style="background:#e8f5ee;color:#1a5c38;" x-text="c.party_list"></span>
                            </template>
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-white text-[12px] font-bold"
                         style="background:linear-gradient(135deg,#1a5c38,#2d7a52);"
                         x-text="i + 1"></div>
                </div>
            </template>
        </div>
    </div>
</div>

</div>
@endsection
