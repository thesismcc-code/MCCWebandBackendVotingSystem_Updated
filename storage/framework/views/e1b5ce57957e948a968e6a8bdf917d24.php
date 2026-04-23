
<?php $__env->startSection('title', 'Election Results — MCC Student Portal'); ?>

<?php $__env->startSection('content'); ?>

<div class="mb-5">
    <h1 class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-0.02em;">Official Election Results</h1>
    <?php if($electionName): ?>
    <p class="text-gray-400 text-sm mt-1">
        <?php echo e($electionName); ?>

        <?php if($publishedAt): ?>
            &nbsp;·&nbsp; Published <?php echo e(\Carbon\Carbon::parse($publishedAt)->format('M d, Y g:i A')); ?>

        <?php endif; ?>
    </p>
    <?php endif; ?>
</div>

<?php if(empty($results)): ?>
<div class="s-card p-12 text-center">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#e8f5ee;">
        <svg class="w-8 h-8" fill="none" stroke="#2d7a52" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <h2 class="text-lg font-extrabold text-gray-700 mb-2">Results Not Yet Published</h2>
    <p class="text-gray-400 text-sm">The official election results haven't been published yet.<br>Check back after the election ends.</p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $candidates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $winner = $candidates[0] ?? null; ?>
    <div class="s-card overflow-hidden">
        
        <div class="px-5 py-3.5 flex items-center gap-2" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <div class="w-1.5 h-1.5 rounded-full bg-green-300"></div>
            <span class="text-[10px] font-bold text-white uppercase tracking-widest"><?php echo e($position); ?></span>
        </div>

        
        <div class="divide-y divide-gray-50">
            <?php $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between px-5 py-3.5 <?php echo e($i === 0 ? '' : ''); ?>"
                 style="<?php echo e($i === 0 ? 'background:#f8fdf9;' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"
                         style="background:<?php echo e($i === 0 ? 'linear-gradient(135deg,#15803d,#16a34a)' : 'linear-gradient(135deg,#9ca3af,#6b7280)'); ?>;">
                        <?php echo e($i === 0 ? '★' : ($i + 1)); ?>

                    </div>
                    <span class="text-sm font-<?php echo e($i === 0 ? 'bold' : 'medium'); ?> <?php echo e($i === 0 ? 'text-gray-900' : 'text-gray-600'); ?>"><?php echo e($c['name']); ?></span>
                    <?php if($i === 0): ?>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:#dcfce7;color:#15803d;">Winner</span>
                    <?php endif; ?>
                </div>
                <span class="text-sm font-bold tabular-nums" style="color:<?php echo e($i === 0 ? '#1a5c38' : '#9ca3af'); ?>;">
                    <?php echo e($c['votes']); ?> <span class="text-xs font-normal"><?php echo e($c['votes'] === 1 ? 'vote' : 'votes'); ?></span>
                </span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if($winner): ?>
        <div class="px-5 py-3 flex items-center justify-between" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="#15803d" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/></svg>
                <span class="text-xs font-extrabold uppercase tracking-wide" style="color:#15803d;">Winner: <?php echo e($winner['name']); ?></span>
            </div>
            <span class="text-sm font-bold" style="color:#15803d;"><?php echo e($winner['votes']); ?> <?php echo e($winner['votes'] === 1 ? 'vote' : 'votes'); ?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.student-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/student/results.blade.php ENDPATH**/ ?>