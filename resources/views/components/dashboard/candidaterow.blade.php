<div class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50/60 transition-colors">
    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"
         style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">
        {{ strtoupper(substr($candidate['name'], 0, 1)) }}
    </div>
    <span class="flex-1 text-sm text-gray-800 font-semibold truncate">{{ $candidate['name'] }}</span>
    <span class="text-sm font-bold tabular-nums" style="color:#1a5c38; min-width:40px; text-align:right;">
        {{ number_format($candidate['votes']) }}
    </span>
    <div class="flex items-center gap-2" style="width:140px;">
        <div class="flex-1 rounded-full overflow-hidden" style="height:6px; background:#e8f5ee;">
            <div class="h-full rounded-full transition-all duration-500"
                 style="width:{{ min($candidate['percentage'], 100) }}%; background:linear-gradient(90deg,#2d7a52,#4CAF7D);"></div>
        </div>
        <span class="text-[11px] font-bold tabular-nums" style="color:#6b9e6b; width:34px; text-align:right;">
            {{ $candidate['percentage'] }}%
        </span>
    </div>
</div>
