<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Control - Fingerprint Voting System</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #102864; }
        .bg-main-panel { background-color: #0C3189; }
        [x-cloak] { display: none !important; }

        /* Helper to style date/time inputs consistent with select arrows */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            background: transparent;
            bottom: 0;
            color: transparent;
            cursor: pointer;
            height: auto;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
        }
    </style>
</head>

<!--
    STATE MANAGEMENT:
    activeModal: 'general' | 'schedule' | 'position' | null
-->
<body x-data="{ activeModal: null }" class="p-4 md:p-6 min-h-screen text-white flex flex-col font-sans relative">

    <!-- HEADER SECTION -->
    <div class="max-w-7xl mx-auto w-full mb-5 flex items-center justify-between px-2">
        <div class="flex items-center gap-4">
            <!-- Back Button -->
            <a href="<?php echo e(route('view.quick-access')); ?>" class="bg-white text-[#113285] rounded-full w-10 h-10 flex items-center justify-center hover:scale-110 transition-transform shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white leading-tight">Election Control</h1>
                <p class="text-blue-200 text-[11px] font-medium">Manage the setup of the voting cycle.</p>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="max-w-7xl mx-auto w-full bg-main-panel rounded-3xl p-6 md:p-10 relative shadow-2xl flex-1 border border-blue-800/30">

        <!-- CARDS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- General Settings Card (Trigger) -->
            <div @click="activeModal = 'general'" class="bg-white rounded-2xl p-5 flex items-center justify-between shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all group cursor-pointer h-28">
                <div class="flex items-center gap-4">
                    <div class="bg-[#0066FF] w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-[#0f172a] font-bold text-lg">General Settings</h3>
                </div>
                <div class="text-[#113285] group-hover:translate-x-1 transition-transform">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path></svg>
                </div>
            </div>

            <!-- Schedule Settings Card (Trigger) -->
            <div @click="activeModal = 'schedule'" class="bg-white rounded-2xl p-5 flex items-center justify-between shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all group cursor-pointer h-28">
                <div class="flex items-center gap-4">
                    <div class="bg-[#00CC00] w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-[#0f172a] font-bold text-lg">Schedule Settings</h3>
                </div>
                <div class="text-[#113285] group-hover:translate-x-1 transition-transform">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path></svg>
                </div>
            </div>

            <!-- Position Setup Card (Trigger) -->
            <a href="<?php echo e(route('view.election-control-posistion-setup')); ?>" class="bg-white rounded-2xl p-5 flex items-center justify-between shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all group cursor-pointer h-28">
                <div class="flex items-center gap-4">
                    <div class="bg-[#FCD34D] w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-[#0f172a] font-bold text-lg">Position Setup</h3>
                </div>
                <div class="text-[#113285] group-hover:translate-x-1 transition-transform">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path></svg>
                </div>
            </a>

        </div>

    </div>

    <!-- ================= MODAL WRAPPER (Handles all modals) ================= -->
    <div
        x-show="activeModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        role="dialog"
        aria-modal="true"
    >
        <!-- Shared Backdrop -->
        <div
            x-show="activeModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
            @click="activeModal = null"
        ></div>

        <!-- ================= 1. SCHEDULE SETTINGS MODAL ================= -->
        <div
            x-show="activeModal === 'schedule'"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-lg p-6 relative z-50"
        >
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Schedule Settings</h2>
            <form action="#" method="POST" class="space-y-4">
                <!-- Election Date Header/Section -->
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-4 flex justify-end pt-2">
                        <span class="text-gray-700 font-medium text-base">Election Date:</span>
                    </div>
                    <div class="col-span-8 space-y-4">
                        <!-- From Date Input -->
                        <div class="grid grid-cols-12 gap-2 items-center">
                            <label class="col-span-2 text-gray-700 font-medium text-right pr-2">From:</label>
                            <div class="col-span-10 relative">
                                <input type="date" class="w-full border border-gray-300 rounded-md p-2.5 text-gray-500 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase" required>
                            </div>
                        </div>
                        <!-- To Date Input -->
                        <div class="grid grid-cols-12 gap-2 items-center">
                            <label class="col-span-2 text-gray-700 font-medium text-right pr-2">To:</label>
                            <div class="col-span-10 relative">
                                <input type="date" class="w-full border border-gray-300 rounded-md p-2.5 text-gray-500 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opening Time -->
                <div class="grid grid-cols-12 gap-4 items-center">
                    <label class="col-span-4 text-gray-700 font-medium text-base text-right">Opening Time:</label>
                    <div class="col-span-8 relative">
                        <input type="time" class="w-full border border-gray-300 rounded-md p-2.5 text-gray-500 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none bg-white border-l border-transparent rounded-r-md">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Closing Time -->
                <div class="grid grid-cols-12 gap-4 items-center">
                    <label class="col-span-4 text-gray-700 font-medium text-base text-right">Closing Time:</label>
                    <div class="col-span-8 relative">
                        <input type="time" class="w-full border border-gray-300 rounded-md p-2.5 text-gray-500 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none bg-white border-l border-transparent rounded-r-md">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3 mt-8 pt-4">
                    <button type="button" @click="activeModal = null" class="px-6 py-2 rounded-md border border-red-500 text-red-500 text-sm font-medium hover:bg-red-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-8 py-2 rounded-md bg-[#22c504] text-white text-sm font-bold hover:bg-green-600 shadow-md transition-colors">Save</button>
                </div>
            </form>
        </div>

        <!-- ================= 2. GENERAL SETTINGS MODAL ================= -->
        <div
            x-show="activeModal === 'general'"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-lg p-6 relative z-50"
        >
            <h2 class="text-2xl font-bold text-gray-900 mb-6">General Settings</h2>
            <form action="#" method="POST" class="space-y-4">
                <!-- Election Name -->
                <div class="grid grid-cols-12 gap-4 items-center">
                    <label class="col-span-4 text-gray-700 font-medium text-base">Election Name:</label>
                    <div class="col-span-8">
                        <input type="text" placeholder="Election Name" class="w-full border border-gray-300 rounded-md p-2.5 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                </div>

                <!-- Semester -->
                <div class="grid grid-cols-12 gap-4 items-center">
                    <label class="col-span-4 text-gray-700 font-medium text-base">Semester:</label>
                    <div class="col-span-8 relative">
                        <select class="w-full border border-gray-300 rounded-md p-2.5 text-gray-500 text-sm appearance-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option>Select Semester</option>
                            <option>1st Semester</option>
                            <option>2nd Semester</option>
                            <option>Summer</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Academic Year -->
                <div class="grid grid-cols-12 gap-4 items-center">
                    <label class="col-span-4 text-gray-700 font-medium text-base">Academic Year:</label>
                    <div class="col-span-8 relative">
                        <select class="w-full border border-gray-300 rounded-md p-2.5 text-gray-500 text-sm appearance-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option>Select Academic Year</option>
                            <option>2025-2026</option>
                            <option>2026-2027</option>
                            <option>2027-2028</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3 mt-8 pt-2">
                    <button type="button" @click="activeModal = null" class="px-6 py-2 rounded-md border border-red-500 text-red-500 text-sm font-medium hover:bg-red-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-8 py-2 rounded-md bg-[#22c504] text-white text-sm font-bold hover:bg-green-600 shadow-md transition-colors">Save</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/electioncontrol.blade.php ENDPATH**/ ?>