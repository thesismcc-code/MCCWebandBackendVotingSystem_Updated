<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-widest mb-5">Year Level Turnout</h4>

    @if(empty($yearLevels))
        <div class="text-center py-6 text-gray-300">
            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-xs font-medium">No data yet</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($yearLevels as $row)
            @php
                $p = $row['turnout_percent'];
                $barColor = $p >= 70 ? '#16a34a' : ($p >= 40 ? '#2d7a52' : '#f59e0b');
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold text-gray-700">{{ $row['year_level'] }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-400">{{ number_format($row['voted']) }}/{{ number_format($row['total_students']) }}</span>
                        <span class="text-xs font-extrabold tabular-nums" style="color:{{ $barColor }};">{{ $p }}%</span>
                    </div>
                </div>
                <div style="height:6px;background:#f0f4eb;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ min($p,100) }}%;background:{{ $barColor }};border-radius:99px;transition:width .7s;"></div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
