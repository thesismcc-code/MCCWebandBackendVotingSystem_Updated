<?php if(session('success') || session('error')): ?>
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 5000)" 
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-12"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-12"
    class="fixed top-8 right-8 z-[200] w-full max-w-xs md:max-w-sm"
>
    <div class="bg-white rounded-[2rem] p-5 shadow-[0_20px_60px_rgba(0,0,0,0.15)] flex items-center border border-gray-50">
        <!-- Dynamic Icon -->
        <div class="flex-shrink-0 mr-4">
            <?php if(session('success')): ?>
                
                <div class="bg-green-500 rounded-full p-2 shadow-sm shadow-green-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            <?php else: ?>
                
                <div class="bg-red-500 rounded-full p-2 shadow-sm shadow-red-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            <?php endif; ?>
        </div>

        <!-- Text Content -->
        <div class="flex-1">
            <h3 class="text-gray-900 font-bold text-lg leading-tight">
                <?php echo e(session('success') ? 'Success!' : 'Error!'); ?>

            </h3>
            <p class="text-gray-400 text-[10px] font-bold mt-0.5 leading-tight uppercase tracking-tight">
                <?php echo e(session('success') ?? session('error')); ?>

            </p>
        </div>

        <!-- Close Button (Optional but recommended) -->
        <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition-colors ml-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/components/notification.blade.php ENDPATH**/ ?>