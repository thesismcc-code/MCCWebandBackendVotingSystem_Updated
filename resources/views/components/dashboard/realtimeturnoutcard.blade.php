<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <div class="flex items-center justify-between mb-5">
        <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest">Real-Time Turnout</h4>
        <span class="flex items-center gap-1.5 text-[10px] font-bold px-2 py-1 rounded-full" style="background:#dcfce7;color:#15803d;">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
            Live
        </span>
    </div>

    @php $pct = $turnout['turnout_percent'] ?? 0; @endphp

    {{-- Circular progress --}}
    <div class="flex items-center gap-5 mb-5">
        <div class="relative flex-shrink-0" style="width:80px;height:80px;">
            <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8f5ee" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#2d7a52" stroke-width="3"
                    stroke-dasharray="{{ $pct }},100" stroke-linecap="round"/>
            </svg>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:14px;font-weight:800;color:#1a5c38;">{{ $pct }}%</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 flex-1">
            <div>
                <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Total</div>
                <div class="text-xl font-extrabold text-gray-900">{{ number_format($turnout['total_students']) }}</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Turnout</div>
                <div class="text-xl font-extrabold" style="color:#1a5c38;">{{ $pct }}%</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Voted</div>
                <div class="text-xl font-extrabold text-green-600">{{ number_format($turnout['voted_count']) }}</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Not Yet</div>
                <div class="text-xl font-extrabold text-orange-500">{{ number_format($turnout['not_yet_voted']) }}</div>
            </div>
        </div>
    </div>

    <div style="height:6px;background:#e8f5ee;border-radius:99px;overflow:hidden;">
        <div style="height:100%;width:{{ min($pct,100) }}%;background:linear-gradient(90deg,#2d7a52,#4CAF7D);border-radius:99px;transition:width .7s;"></div>
    </div>
</div>
