<?php $__env->startSection('title', 'SAO Dashboard'); ?>

<?php $__env->startSection('content'); ?>

    <!-- Dashboard Heading -->
    <h2 class="text-[1.75rem] text-white font-bold mb-6">Dashboard</h2>

    <!-- GRID LAYOUT (2 Column Block)-->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

        <!-- 1. ELECTION OVERVIEW CARD (Border Cyan Theme Highlight Mapped directly nicely) -->
        <div class="lg:col-span-8 bg-white rounded-lg p-7 border-[4px] border-[#38a9fa] text-black">
            <h5 class="text-lg font-bold uppercase tracking-wider mb-6">ELECTION OVERVIEW</h5>

            <!-- Stats Details Map -->
            <div class="grid grid-cols-[150px_auto] gap-y-4">
                <div class="font-bold text-gray-900 text-[1.05rem]">Election Name:</div>
                <div class="text-[1.05rem]">MCC ELECTION 2025</div>

                <div class="font-bold text-gray-900 text-[1.05rem]">Election Status:</div>
                <div class="text-[1.05rem]">Ongoing</div>

                <div class="font-bold text-gray-900 text-[1.05rem]">Results Status:</div>
                <div class="text-[#dc3545] text-[1.05rem]">Not Published</div>
            </div>
        </div>

        <!-- 2. VOTER TURNOUT CARD (Basic White Rounded Block Theme Component Native Box) -->
        <div class="lg:col-span-4 bg-white rounded-lg p-7 text-black flex flex-col justify-between">
            <h5 class="text-lg font-bold uppercase tracking-wider mb-6">VOTER TURNOUT</h5>

            <div class="grid grid-cols-2 gap-y-5 gap-x-2 w-full text-[1rem]">
                <!-- Item Block Box Stat Info Group Row Grid Box Native  -->
                <div>
                    <div class="mb-[0.2rem] text-gray-800">Total Voters:</div>
                    <div class="text-2xl font-bold leading-tight">100</div>
                </div>
                <div>
                    <div class="mb-[0.2rem] text-gray-800">Turnout:</div>
                    <div class="text-2xl font-bold leading-tight">53%</div>
                </div>
                <div>
                    <div class="mb-[0.2rem] text-gray-800">Voted:</div>
                    <div class="text-2xl font-bold leading-tight">53</div>
                </div>
                <div>
                    <div class="mb-[0.2rem] text-gray-800">Not Yet:</div>
                    <div class="text-2xl font-bold leading-tight">47</div>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. PER YEAR LEVEL TURNOUT PROGRESS MAP DATA BLOCK MAPPING VIEW STAT ROW LINE VIEW NATIVE MAP BLOCK VIEW) -->
    <div class="bg-white rounded-lg p-7 text-black mt-2">
        <h5 class="text-lg font-bold uppercase tracking-wider mb-8">PER YEAR LEVEL TURNOUT</h5>

        <div class="flex flex-col space-y-5 pl-2 pr-6 mb-2">
            <!-- Level Container Items Layout Bars Scale Array Render Directly Explicitly Explicit Fully Completely Component View Info Native Box Container Block Block Row Layout Line Maps Bar Bar Stat Scale Block Maps Native Info Block) -->
            <!-- 1st Year Layout Container Bar Map Progress Track Info Group View Box Stat Bar) -->
            <div class="flex flex-row items-center w-full">
                <div class="w-28 text-base">1st Year</div>
                <div class="flex-1 mx-3 h-2.5 bg-[#e2e8f0] rounded-full overflow-hidden">
                    <div class="h-full bg-[#111827] rounded-full" style="width: 80%;"></div>
                </div>
                <div class="w-16 text-right text-base font-normal">80%</div>
            </div>

            <!-- 2nd Year Track Line Progress Display UI Track Group Display -->
            <div class="flex flex-row items-center w-full">
                <div class="w-28 text-base">2nd Year</div>
                <div class="flex-1 mx-3 h-2.5 bg-[#e2e8f0] rounded-full overflow-hidden">
                    <div class="h-full bg-[#111827] rounded-full" style="width: 67%;"></div>
                </div>
                <div class="w-16 text-right text-base font-normal">67%</div>
            </div>

            <!-- 3rd Year Track Line Progress Info Graphic Maps Render Stat Frame -->
            <div class="flex flex-row items-center w-full">
                <div class="w-28 text-base">3rd Year</div>
                <div class="flex-1 mx-3 h-2.5 bg-[#e2e8f0] rounded-full overflow-hidden">
                    <div class="h-full bg-[#111827] rounded-full" style="width: 43%;"></div>
                </div>
                <div class="w-16 text-right text-base font-normal">43%</div>
            </div>

            <!-- 4th Year Stat Layout Layout Map Box Info Box Data Component Track Data Container Element Visual Format Chart Native Maps Stat Item Progress Tracker )-->
            <div class="flex flex-row items-center w-full">
                <div class="w-28 text-base">4th Year</div>
                <div class="flex-1 mx-3 h-2.5 bg-[#e2e8f0] rounded-full overflow-hidden">
                    <div class="h-full bg-[#111827] rounded-full" style="width: 31%;"></div>
                </div>
                <div class="w-16 text-right text-base font-normal">31%</div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.sao-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/saodashboard.blade.php ENDPATH**/ ?>