@extends('components.sao-layout')
@section('title', 'Final Results — MCC Voting System')
@section('page-title', 'Final Results')
@section('page-sub', 'Official election results')

@section('content')

{{-- Publish Button --}}
<div class="flex justify-end mb-5">
    @if($isPublished)
        <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white" style="background:linear-gradient(135deg,#15803d,#16a34a);box-shadow:0 4px 12px rgba(21,128,61,.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Results Published
        </span>
    @else
        <button id="triggerConfirmBtn"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg"
            style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 12px rgba(26,92,56,.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Publish Official Results
        </button>
    @endif
</div>

@if(empty($results))
    <div class="text-center py-16">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-gray-400 text-sm font-semibold">No results available yet.</p>
    </div>
@else
    {{-- Results Grid --}}
    @php
        $positions = array_keys($results);
        $half  = (int) ceil(count($positions) / 2);
        $left  = array_slice($positions, 0, $half);
        $right = array_slice($positions, $half);
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Left Column --}}
        <div class="flex flex-col gap-5">
            @foreach($left as $pos)
            @php $cands = $results[$pos]; $winner = $cands[0] ?? null; @endphp
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                {{-- Position header --}}
                <div class="px-5 py-3.5 flex items-center gap-2" style="background:#f8faf6;border-bottom:1px solid #e8ede3;">
                    <div class="w-2 h-2 rounded-full" style="background:#2d7a52;"></div>
                    <span class="font-extrabold text-xs text-gray-700 uppercase tracking-widest">{{ $pos }}</span>
                </div>
                {{-- Candidates --}}
                <div class="divide-y divide-gray-50">
                    @foreach($cands as $i => $c)
                    <div class="flex items-center justify-between px-5 py-3.5 {{ $i === 0 ? '' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"
                                 style="background:{{ $i === 0 ? 'linear-gradient(135deg,#15803d,#16a34a)' : 'linear-gradient(135deg,#9ca3af,#6b7280)' }};">
                                {{ $i === 0 ? '★' : ($i + 1) }}
                            </div>
                            <span class="text-sm font-{{ $i === 0 ? 'bold' : 'medium' }} {{ $i === 0 ? 'text-gray-900' : 'text-gray-600' }}">{{ $c['name'] }}</span>
                            @if($i === 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:#dcfce7;color:#15803d;">Winner</span>
                            @endif
                        </div>
                        <span class="text-sm font-bold tabular-nums" style="color:{{ $i === 0 ? '#1a5c38' : '#6b7280' }};">{{ number_format($c['votes']) }} votes</span>
                    </div>
                    @endforeach
                </div>
                @if($winner)
                <div class="px-5 py-3 flex items-center justify-between" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="#15803d" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/></svg>
                        <span class="text-xs font-extrabold uppercase tracking-wide" style="color:#15803d;">Winner: {{ $winner['name'] }}</span>
                    </div>
                    <span class="text-sm font-bold" style="color:#15803d;">{{ number_format($winner['votes']) }} votes</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Right Column --}}
        <div class="flex flex-col gap-5">
            @foreach($right as $pos)
            @php $cands = $results[$pos]; $winner = $cands[0] ?? null; @endphp
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                <div class="px-5 py-3.5 flex items-center gap-2" style="background:#f8faf6;border-bottom:1px solid #e8ede3;">
                    <div class="w-2 h-2 rounded-full" style="background:#2d7a52;"></div>
                    <span class="font-extrabold text-xs text-gray-700 uppercase tracking-widest">{{ $pos }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($cands as $i => $c)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"
                                 style="background:{{ $i === 0 ? 'linear-gradient(135deg,#15803d,#16a34a)' : 'linear-gradient(135deg,#9ca3af,#6b7280)' }};">
                                {{ $i === 0 ? '★' : ($i + 1) }}
                            </div>
                            <span class="text-sm font-{{ $i === 0 ? 'bold' : 'medium' }} {{ $i === 0 ? 'text-gray-900' : 'text-gray-600' }}">{{ $c['name'] }}</span>
                            @if($i === 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:#dcfce7;color:#15803d;">Winner</span>
                            @endif
                        </div>
                        <span class="text-sm font-bold tabular-nums" style="color:{{ $i === 0 ? '#1a5c38' : '#6b7280' }};">{{ number_format($c['votes']) }} votes</span>
                    </div>
                    @endforeach
                </div>
                @if($winner)
                <div class="px-5 py-3 flex items-center justify-between" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="#15803d" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/></svg>
                        <span class="text-xs font-extrabold uppercase tracking-wide" style="color:#15803d;">Winner: {{ $winner['name'] }}</span>
                    </div>
                    <span class="text-sm font-bold" style="color:#15803d;">{{ number_format($winner['votes']) }} votes</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Confirm Modal --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background:rgba(0,0,0,.55);backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center mx-4">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#fee2e2;">
            <svg width="30" height="30" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-2">Are you sure?</h3>
        <p class="text-sm text-gray-500 font-medium mb-6">You want to publish the official results? This action cannot be undone.</p>
        <div class="flex gap-3 justify-center">
            <button id="cancelBtn" class="px-6 py-2.5 rounded-xl border-2 border-red-400 text-red-500 font-bold text-sm hover:bg-red-50 transition-colors">Cancel</button>
            <button id="confirmSubmitBtn" class="px-6 py-2.5 rounded-xl text-white font-bold text-sm transition-all hover:-translate-y-0.5" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 12px rgba(26,92,56,.3);">Publish</button>
        </div>
    </div>
</div>

{{-- Success Modal --}}
<div id="successModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background:rgba(0,0,0,.55);backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center mx-4">
        <div class="w-16 h-16 rounded-full border-4 border-green-500 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-2">Published!</h3>
        <p class="text-sm text-gray-500 font-medium">Official results are now visible on all student dashboards.</p>
        <button onclick="document.getElementById('successModal').classList.replace('flex','hidden');document.getElementById('successModal').classList.add('hidden');"
            class="mt-5 px-8 py-2.5 rounded-xl text-white font-bold text-sm" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">OK</button>
    </div>
</div>

<form id="publishForm" action="{{ route('sao.publish-results') }}" method="POST" style="display:none;">@csrf</form>

<script>
    const confirmModal = document.getElementById('confirmModal');
    const successModal = document.getElementById('successModal');
    const triggerBtn   = document.getElementById('triggerConfirmBtn');
    const cancelBtn    = document.getElementById('cancelBtn');
    const submitBtn    = document.getElementById('confirmSubmitBtn');

    @if(session('success'))
        successModal.classList.remove('hidden');
        successModal.classList.add('flex');
    @endif

    if (triggerBtn) {
        triggerBtn.addEventListener('click', () => {
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
        });
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            confirmModal.classList.replace('flex','hidden');
        });
    }
    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            confirmModal.classList.replace('flex','hidden');
            document.getElementById('publishForm').submit();
        });
    }
</script>
@endsection
