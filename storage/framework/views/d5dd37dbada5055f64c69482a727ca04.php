<?php $__env->startSection('title', 'Admin Dashboard Overview'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* Specific styling to match the image exact colors */
        .dashboard-bg {
            background-color: #f4f6f0;
            min-height: 100vh;
            color: #1a3a1a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 1.5rem;
        }

        .custom-card {
            background-color: white;
            border: 1px solid #e8ede3;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            height: 100%;
            padding: 1.5rem;
            position: relative;
        }

        /* Styling for the green highlight border on the active/selected card */
        .card-selected-border {
            border: 3px solid #2d7a52;
        }

        /* Card Title Styling */
        .card-title-custom {
            color: #1a5c38;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            margin-bottom: 1.5rem;
        }

        /* Stats Labels */
        .stat-label {
            color: #374151;
            /* Dark Gray */
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        /* Stats Values */
        .stat-value {
            color: #000;
            font-weight: 800;
            font-size: 1.25rem;
            line-height: 1.2;
        }

        /* 'Not Yet' Link Styling */
        .stat-link {
            color: #000;
            text-decoration: underline;
            text-decoration-color: #1a4bbc;
            text-decoration-thickness: 3px;
            /* Thicker underline to match image */
            text-underline-offset: 3px;
            cursor: pointer;
        }

        /* Progress Bars */
        .progress-track {
            height: 16px;
            /* Slightly taller */
            background-color: #e5e5e5;
            border-radius: 50px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .progress-fill {
            background-color: #1c1c1c;
            /* Black fill */
            border-radius: 50px;
        }

        /* Year Rows Typography */
        .year-text {
            width: 80px;
            font-weight: 500;
            color: #1f2937;
            font-size: 0.95rem;
        }

        .percent-text {
            width: 50px;
            text-align: right;
            font-weight: 600;
            color: #374151;
            font-size: 0.95rem;
        }
    </style>

    <div class="min-h-screen  p-6 font-sans">
        <!-- Header -->
        <h3 class="text-2xl font-bold mb-6" style="color:#1a3a1a;">Dashboard</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card 1: Real Time Voter Turnout -->
            <div class="bg-white rounded-xl p-6 border-[3px] border-[#2d7a52] shadow-md relative h-full">
                <h5 class="font-extrabold uppercase text-sm tracking-wide mb-6" style="color:#1a5c38;">
                    REAL TIME VOTER TURNOUT
                </h5>

                <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                    <!-- Total Voters -->
                    <div>
                        <div class="text-gray-600 text-sm font-medium mb-1">Total Voters:</div>
                        <div class="text-black text-2xl font-extrabold">100</div>
                    </div>

                    <!-- Turnout -->
                    <div>
                        <div class="text-gray-600 text-sm font-medium mb-1">Turnout:</div>
                        <div class="text-black text-2xl font-extrabold">10%</div>
                    </div>

                    <!-- Voted -->
                    <div>
                        <div class="text-gray-600 text-sm font-medium mb-1">Voted:</div>
                        <div class="text-black text-2xl font-extrabold">53</div>
                    </div>

                    <!-- Not Yet -->
                    <div>
                        <div class="text-gray-600 text-sm font-medium mb-1">Not Yet:</div>
                        <div
                            class="text-black text-2xl font-extrabold underline decoration-[#1a4bbc] decoration-[3px] underline-offset-4 cursor-pointer">
                            47
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Per Year Level Turnout -->
            <div class="bg-white rounded-xl p-6 shadow-md h-full" style="border:1px solid #e8ede3;">
                <h5 class="font-extrabold uppercase text-sm tracking-wide mb-6" style="color:#1a5c38;">
                    PER YEAR LEVEL TURNOUT
                </h5>

                <div class="flex flex-col gap-4">
                    <!-- 1st Year -->
                    <div class="flex items-center">
                        <div class="w-20 text-gray-800 font-medium">1st Year</div>
                        <div class="flex-1 h-4 bg-gray-200 rounded-full mx-3 shadow-inner relative overflow-hidden">
                            <div class="h-full bg-gray-900 rounded-full" style="width: 80%"></div>
                        </div>
                        <div class="w-10 text-right text-gray-700 font-bold text-sm">80%</div>
                    </div>

                    <!-- 2nd Year -->
                    <div class="flex items-center">
                        <div class="w-20 text-gray-800 font-medium">2nd Year</div>
                        <div class="flex-1 h-4 bg-gray-200 rounded-full mx-3 shadow-inner relative overflow-hidden">
                            <div class="h-full bg-gray-900 rounded-full" style="width: 67%"></div>
                        </div>
                        <div class="w-10 text-right text-gray-700 font-bold text-sm">67%</div>
                    </div>

                    <!-- 3rd Year -->
                    <div class="flex items-center">
                        <div class="w-20 text-gray-800 font-medium">3rd Year</div>
                        <div class="flex-1 h-4 bg-gray-200 rounded-full mx-3 shadow-inner relative overflow-hidden">
                            <div class="h-full bg-gray-900 rounded-full" style="width: 43%"></div>
                        </div>
                        <div class="w-10 text-right text-gray-700 font-bold text-sm">43%</div>
                    </div>

                    <!-- 4th Year -->
                    <div class="flex items-center">
                        <div class="w-20 text-gray-800 font-medium">4th Year</div>
                        <div class="flex-1 h-4 bg-gray-200 rounded-full mx-3 shadow-inner relative overflow-hidden">
                            <div class="h-full bg-gray-900 rounded-full" style="width: 31%"></div>
                        </div>
                        <div class="w-10 text-right text-gray-700 font-bold text-sm">31%</div>
                    </div>
                </div>
            </div>


        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.comelec-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/comelecdashboard.blade.php ENDPATH**/ ?>