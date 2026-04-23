<a href="<?php echo e($route); ?>" class="bg-white rounded-2xl p-6 flex flex-col h-48 justify-between shadow-xl hover:scale-[1.02] transition-transform duration-200 group">
    <div>
        <!-- Icon Container -->
        <div class="<?php echo e($icon_bg); ?> w-12 h-12 rounded-lg flex items-center justify-center text-white mb-4">
            <img
                src="<?php echo e($icon_path); ?>"
                alt="Fingerprint Scanner"
                class="w-8 h-8 object-contain"
            >
        </div>

        <!-- Text content -->
        <h3 class="text-gray-900 font-bold text-lg leading-tight"><?php echo e($title); ?></h3>
        <p class="text-gray-500 text-[11px] mt-1"><?php echo e($desc); ?></p>
    </div>

    <!-- Arrow Button -->
    <div class="flex justify-end">
        <div class="bg-blue-900 text-white p-2 rounded-full group-hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
        </div>
    </div>
</a>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/components/quick-access-card.blade.php ENDPATH**/ ?>