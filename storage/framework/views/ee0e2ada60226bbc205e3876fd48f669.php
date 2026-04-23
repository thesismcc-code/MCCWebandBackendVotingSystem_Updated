<?php
    $colorMap = [
        'blue'   => 'bg-blue-600',
        'green'  => 'bg-green-500',
        'yellow' => 'bg-yellow-500',
        'red'    => 'bg-red-500',
    ];
    $iconBg = $colorMap[$color] ?? 'bg-blue-600';
?>

<div class="bg-white rounded-2xl p-4 flex items-center gap-4 shadow-lg">

    <div class="p-3 <?php echo e($iconBg); ?> rounded-xl flex-shrink-0">

        <?php if($icon === 'user'): ?>
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
            </svg>

        <?php elseif($icon === 'check-circle'): ?>
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>

        <?php elseif($icon === 'users'): ?>
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>

        <?php elseif($icon === 'percent'): ?>
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
            </svg>
        <?php endif; ?>

    </div>

    <div>
        <div class="text-3xl font-bold text-gray-900 leading-tight"><?php echo e($value); ?></div>
        <div class="text-[10px] text-gray-500 uppercase font-semibold tracking-wide mt-0.5 text-nowrap">
            <?php echo e($label); ?>

        </div>
    </div>

</div>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/components/dashboard/statcard.blade.php ENDPATH**/ ?>