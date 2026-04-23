<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Logs - Fingerprint Voting System</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #102864; }
        .bg-main-panel { background-color: #0C3189; }
        .bg-input-dark { background-color: #113285; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar for table if needed */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>

<body class="p-4 md:p-6 min-h-screen text-white flex flex-col font-sans relative">

    <!-- HEADER SECTION -->
    <div class="max-w-7xl mx-auto w-full mb-5 flex items-center justify-between px-2">
        <div class="flex items-center gap-4">
            <!-- Back Button -->
            <a href="<?php echo e(route('view.quick-access')); ?>" class="bg-white text-[#113285] rounded-full w-10 h-10 flex items-center justify-center hover:scale-110 transition-transform shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white leading-tight">Voting Logs</h1>
                <p class="text-blue-200 text-[11px] font-medium">Track every voting</p>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="max-w-7xl mx-auto w-full bg-main-panel rounded-3xl p-6 relative shadow-2xl flex-1 flex flex-col">

        <!-- TOP CARDS (Security & Export) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <!-- Security Logs Card -->
            <div class="bg-white rounded-xl p-4 flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-4">
                    <div class="bg-[#0055ff] w-12 h-12 rounded-xl flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.956 11.956 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-900 font-bold text-lg">Security Logs</h3>
                        <p class="text-gray-500 text-xs leading-tight">Monitor suspicious and failed authentication<br>attempts</p>
                    </div>
                </div>
                <a href="#" class="text-[#102864] hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            </div>

            <!-- Export PDF Card -->
            <div class="bg-white rounded-xl p-4 flex items-center gap-4 shadow-lg cursor-pointer hover:bg-gray-50 transition-colors">
                <div class="bg-[#00e626] w-12 h-12 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </div>
                <div>
                    <h3 class="text-gray-900 font-bold text-lg">Export to PDF</h3>
                </div>
            </div>
        </div>

        <!-- FILTERS ROW -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <!-- Search -->
            <div class="md:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text"
                    class="bg-input-dark w-full border border-blue-400/50 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-3 placeholder-blue-200/70"
                    placeholder="Search by Student ID or Name">
            </div>

            <!-- Course Filter -->
            <div class="md:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
                <select class="bg-input-dark w-full border border-blue-400/50 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-3 appearance-none cursor-pointer">
                    <option>All Courses</option>
                    <option>Computer Science</option>
                    <option>Information Technology</option>
                    <option>Business Administration</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Year Level Filter -->
            <div class="md:col-span-3 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <select class="bg-input-dark w-full border border-blue-400/50 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-3 appearance-none cursor-pointer">
                    <option>Year Level</option>
                    <option>1st Year</option>
                    <option>2nd Year</option>
                    <option>3rd Year</option>
                    <option>4th Year</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-xl flex-1">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="p-4 text-sm font-bold text-gray-900">Student ID</th>
                            <th class="p-4 text-sm font-bold text-gray-900">Name</th>
                            <th class="p-4 text-sm font-bold text-gray-900">Course</th>
                            <th class="p-4 text-sm font-bold text-gray-900">Year Level</th>
                            <th class="p-4 text-sm font-bold text-gray-900">Date & Time</th>
                            <th class="p-4 text-sm font-bold text-gray-900 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Example Row 1 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-700 font-medium">CS-2025-001</td>
                            <td class="p-4 text-sm text-gray-700">Jose Perolino</td>
                            <td class="p-4 text-sm text-gray-700">Computer Science</td>
                            <td class="p-4 text-sm text-gray-700">4th Year</td>
                            <td class="p-4 text-sm text-gray-700">12-12-2025 10:43AM</td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Voted</span>
                            </td>
                        </tr>
                        <!-- Example Row 2 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-700 font-medium">IT-2025-035</td>
                            <td class="p-4 text-sm text-gray-700">Myles Macrohon</td>
                            <td class="p-4 text-sm text-gray-700">Information Technology</td>
                            <td class="p-4 text-sm text-gray-700">2nd Year</td>
                            <td class="p-4 text-sm text-gray-700">13-12-2025 1:30PM</td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Voted</span>
                            </td>
                        </tr>
                        <!-- Example Row 3 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-700 font-medium">BA-2025-141</td>
                            <td class="p-4 text-sm text-gray-700">Honey Malang</td>
                            <td class="p-4 text-sm text-gray-700">Business Administration</td>
                            <td class="p-4 text-sm text-gray-700">3rd Year</td>
                            <td class="p-4 text-sm text-gray-700">14-12-2025 8:41AM</td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Voted</span>
                            </td>
                        </tr>
                         <!-- Example Row 4 -->
                         <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-700 font-medium">CS-2025-225</td>
                            <td class="p-4 text-sm text-gray-700">Jahaira Ampaso</td>
                            <td class="p-4 text-sm text-gray-700">Business Administration</td>
                            <td class="p-4 text-sm text-gray-700">1st Year</td>
                            <td class="p-4 text-sm text-gray-700">14-12-2025 10:43AM</td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Voted</span>
                            </td>
                        </tr>
                        <!-- Example Row 5 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-700 font-medium">IT-2025-005</td>
                            <td class="p-4 text-sm text-gray-700">James Cortes</td>
                            <td class="p-4 text-sm text-gray-700">Information Technology</td>
                            <td class="p-4 text-sm text-gray-700">1st Year</td>
                            <td class="p-4 text-sm text-gray-700">14-12-2025 1:30PM</td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Voted</span>
                            </td>
                        </tr>
                        <!-- Example Row 6 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-700 font-medium">BA-2025-110</td>
                            <td class="p-4 text-sm text-gray-700">Carl Cobarde</td>
                            <td class="p-4 text-sm text-gray-700">Computer Science</td>
                            <td class="p-4 text-sm text-gray-700">3rd Year</td>
                            <td class="p-4 text-sm text-gray-700">12-12-2025 10:43AM</td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Voted</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4 flex justify-center items-center gap-2">
            <button class="w-8 h-8 rounded bg-white text-[#0C3189] font-bold text-sm shadow hover:bg-gray-100">1</button>
            <button class="w-8 h-8 rounded border border-white/30 text-white font-medium text-sm hover:bg-white/10">2</button>
            <button class="w-8 h-8 rounded border border-white/30 text-white flex items-center justify-center hover:bg-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>

    </div>
</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/votinglogs.blade.php ENDPATH**/ ?>