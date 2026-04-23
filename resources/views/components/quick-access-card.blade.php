<a href="{{ $route }}"
   class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-3xl p-7 flex flex-col justify-between border border-gray-100/80 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden backdrop-blur-sm"
   style="min-height:180px;">

    {{-- Enhanced background accent --}}
    <div class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-[0.03] -translate-y-8 translate-x-8 transition-all duration-500 group-hover:opacity-[0.08] group-hover:scale-125 group-hover:rotate-12"
         style="background:linear-gradient(135deg,#2d7a52,#1a5c38);"></div>
    
    {{-- Subtle top border accent --}}
    <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-green-200/60 to-transparent"></div>

    <div>
        <div class="{{ $icon_bg }} w-14 h-14 rounded-2xl flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 relative overflow-hidden">
            {{-- Icon background glow --}}
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
            <img src="{{ $icon_path }}" alt="{{ $title }}" class="w-7 h-7 object-contain relative z-10" style="filter:brightness(0) invert(1);">
        </div>
        <h3 class="text-gray-900 font-bold text-[16px] leading-tight mb-2 group-hover:text-green-800 transition-colors duration-200">{{ $title }}</h3>
        <p class="text-gray-500 text-[13px] font-medium leading-relaxed">{{ $desc }}</p>
    </div>

    <div class="flex justify-end mt-6">
        <div class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:rotate-12 relative overflow-hidden"
             style="background:linear-gradient(135deg,#2d7a52,#1a5c38); box-shadow:0 4px 16px rgba(26,92,56,.25);">
            {{-- Button glow effect --}}
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
            <svg class="w-5 h-5 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </div>
    </div>
</a>
