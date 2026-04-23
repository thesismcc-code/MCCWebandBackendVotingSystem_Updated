<?php $__env->startSection('MCC', 'Voting System'); ?>
<?php $__env->startSection('content'); ?>

    <h2 class="text-2xl font-bold text-white mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <?php echo $__env->make('components.dashboard.statcard', [
            'value' => $data['stats_card_data']['total_register_voters'],
            'label' => 'Total Registered Voters',
            'color' => 'blue',
            'icon' => 'user',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('components.dashboard.statcard', [
            'value' => $data['stats_card_data']['live_vote_cast'],
            'label' => 'Live Votes Cast',
            'color' => 'green',
            'icon' => 'check-circle',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('components.dashboard.statcard', [
            'value' => $data['stats_card_data']['running_candidates'],
            'label' => 'Running Candidates',
            'color' => 'yellow',
            'icon' => 'users',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('components.dashboard.statcard', [
            'value' => $data['stats_card_data']['turn_out_rates']['turnout_percent'] . '%',
            'label' => 'Turnout Rates',
            'color' => 'red',
            'icon' => 'percent',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>

    
    <div class="flex items-center gap-2 mb-5">
        <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse inline-block"></span>
        <h3 class="text-white font-extrabold text-sm tracking-widest uppercase">Live Candidate Results</h3>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        
        <div class="lg:col-span-3 flex flex-col gap-5">

            <?php $__empty_1 = true; $__currentLoopData = $data['live_candidate_result']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $candidates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php echo $__env->make('components.dashboard.candidateposistioncard', [
                    'position' => $position,
                    'candidates' => $candidates,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white/10 rounded-2xl p-8 text-center text-white/50 italic">
                    No candidate results available yet.
                </div>
            <?php endif; ?>

        </div>

        
        <div class="lg:col-span-2 flex flex-col gap-5">

            <?php echo $__env->make('components.dashboard.realtimeturnoutcard', [
                'turnout' => $data['realtime_turnout'],
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('components.dashboard.yearlevelturnoutcard', [
                'yearLevels' => $data['per_year_level_turnout'],
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>

    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/dashboard.blade.php ENDPATH**/ ?>