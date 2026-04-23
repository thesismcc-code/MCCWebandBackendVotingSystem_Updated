<div class="bg-white rounded-2xl p-6 shadow-lg">

    <h4 class="text-xs font-extrabold text-gray-900 uppercase tracking-widest mb-5">
        Real Time Voter Turnout
    </h4>

    <div class="grid grid-cols-2 gap-x-4 gap-y-5">

        
        <div>
            <div class="text-xs text-gray-500 font-medium mb-1">Total Voters:</div>
            <div class="text-3xl font-extrabold text-gray-900 leading-none">
                <?php echo e(number_format($turnout['total_students'])); ?>

            </div>
        </div>

        
        <div>
            <div class="text-xs text-gray-500 font-medium mb-1">Turnout:</div>
            <div class="text-3xl font-extrabold text-gray-900 leading-none">
                <?php echo e($turnout['turnout_percent']); ?>%
            </div>
        </div>

        
        <div>
            <div class="text-xs text-gray-500 font-medium mb-1">Voted:</div>
            <div class="text-3xl font-extrabold text-green-600 leading-none">
                <?php echo e(number_format($turnout['voted_count'])); ?>

            </div>
        </div>

        
        <div>
            <div class="text-xs text-gray-500 font-medium mb-1">Not Yet:</div>
            <div class="text-3xl font-extrabold text-red-500 leading-none">
                <?php echo e(number_format($turnout['not_yet_voted'])); ?>

            </div>
        </div>

    </div>

</div>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/components/dashboard/realtimeturnoutcard.blade.php ENDPATH**/ ?>