<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100" style="background:#f8faf6;">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full" style="background:#2d7a52;"></div>
            <span class="font-extrabold text-xs text-gray-700 uppercase tracking-widest">{{ $position }}</span>
        </div>
        <div class="flex items-center gap-6">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider w-14 text-center">Votes</span>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider w-36 text-center">Progress</span>
        </div>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($candidates as $candidate)
            @include('components.dashboard.candidaterow', ['candidate' => $candidate])
        @endforeach
    </div>
</div>
