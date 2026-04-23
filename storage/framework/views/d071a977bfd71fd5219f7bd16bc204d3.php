

<div class="bg-white rounded-2xl p-6 shadow-lg">

    <h4 class="text-xs font-extrabold text-gray-900 uppercase tracking-widest mb-5">
        Per Year Level Turnout
    </h4>

    <?php if(empty($yearLevels)): ?>
        <p class="text-gray-400 italic text-sm">No data available.</p>
    <?php else: ?>
        <div class="flex flex-col gap-4">
            <?php $__currentLoopData = $yearLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <div>
                    
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-semibold text-gray-800">
                            <?php echo e($row['year_level']); ?>

                        </span>
                        <span class="text-sm font-bold text-gray-900 tabular-nums">
                            <?php echo e($row['turnout_percent']); ?>%
                        </span>
                    </div>

                    
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-gray-900 transition-all duration-500"
                             style="width: <?php echo e(min($row['turnout_percent'], 100)); ?>%">
                        </div>
                    </div>

                    
                    <div class="flex justify-between mt-1">
                        <span class="text-[10px] text-gray-400">
                            <?php echo e(number_format($row['voted'])); ?> voted
                        </span>
                        <span class="text-[10px] text-gray-400">
                            of <?php echo e(number_format($row['total_students'])); ?>

                        </span>
                    </div>
                </div>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

</div>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/components/dashboard/yearlevelturnoutcard.blade.php ENDPATH**/ ?>