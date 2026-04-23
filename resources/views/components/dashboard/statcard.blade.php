@php
    $colorMap = [
        'blue'   => ['bg' => '#e8f5ee', 'icon' => '#2d7a52', 'text' => '#1a5c38'],
        'green'  => ['bg' => '#dcfce7', 'icon' => '#16a34a', 'text' => '#15803d'],
        'yellow' => ['bg' => '#fef9c3', 'icon' => '#ca8a04', 'text' => '#a16207'],
        'red'    => ['bg' => '#fee2e2', 'icon' => '#dc2626', 'text' => '#b91c1c'],
    ];
    $c = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-4">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:{{ $c['bg'] }};">
            @if($icon === 'user')
                <svg class="w-5 h-5" fill="none" stroke="{{ $c['icon'] }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            @elseif($icon === 'check-circle')
                <svg class="w-5 h-5" fill="none" stroke="{{ $c['icon'] }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @elseif($icon === 'users')
                <svg class="w-5 h-5" fill="none" stroke="{{ $c['icon'] }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            @elseif($icon === 'percent')
                <svg class="w-5 h-5" fill="none" stroke="{{ $c['icon'] }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            @endif
        </div>
        <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};">Live</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-900 leading-none mb-1">{{ $value }}</div>
    <div class="text-xs font-600 text-gray-400 uppercase tracking-wide">{{ $label }}</div>
</div>
