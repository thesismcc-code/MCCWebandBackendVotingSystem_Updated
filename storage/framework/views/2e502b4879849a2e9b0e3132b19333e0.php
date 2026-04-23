<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b1e47;
            /* Deep Navy Background */
        }

        .bg-main-panel {
            background-color: #0b2e7a;
            /* Lighter Royal Blue Panel */
        }

        [x-cloak] {
            display: none !important;
        }

        /* Form elements overrides for image match */
        .form-input {
            border-color: #d1d5db;
            /* gray-300 */
        }

        .form-input:focus {
            border-color: #3b82f6;
            /* blue-500 */
            box-shadow: 0 0 0 1px #3b82f6;
            outline: none;
        }
    </style>
</head>

<body x-data="{ activeModal: null, showAddCandidate: false, showSuccess: false }"
    class="p-4 md:p-8 min-h-screen text-white flex flex-col font-sans antialiased selection:bg-blue-500 selection:text-white">

    <!-- HEADER SECTION -->
    <div class="max-w-7xl mx-auto w-full mb-6 flex items-center gap-4 px-2">
        <a href="<?php echo e(route('view.comelec-dashboard')); ?>"
            class="bg-white text-[#0b2e7a] rounded-full w-10 h-10 flex items-center justify-center hover:scale-105 transition-transform shadow-md flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-white">Manage Candidates</h1>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="max-w-7xl mx-auto w-full bg-main-panel rounded-[2rem] p-8 shadow-2xl min-h-[600px] relative">

        <h2 class="text-white font-bold text-lg tracking-wide uppercase mb-8">POSITIONS</h2>

        <!-- CARDS GRID (Visually muted if ANY modal is open) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-300"
            :class="(activeModal || showAddCandidate || showSuccess) ? 'opacity-30 pointer-events-none' : 'opacity-100'">

            <!-- PRESIDENT CARD -->
            <div @click="activeModal = 'president'"
                class="bg-white rounded-xl p-6 h-32 relative shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h3 class="text-black font-extrabold text-[0.95rem] uppercase tracking-wide">PRESIDENT</h3>
                        <p class="text-black font-bold text-lg mt-1">2</p>
                    </div>
                </div>
                <div
                    class="absolute bottom-5 right-5 w-8 h-8 rounded-full bg-[#103080] flex items-center justify-center group-hover:bg-[#0c2466] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </div>

            <!-- VICE PRESIDENT CARD -->
            <div
                class="bg-white rounded-xl p-6 h-32 relative shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h3 class="text-black font-extrabold text-[0.95rem] uppercase tracking-wide">VICE PRESIDENT</h3>
                        <p class="text-black font-bold text-lg mt-1">2</p>
                    </div>
                </div>
                <div
                    class="absolute bottom-5 right-5 w-8 h-8 rounded-full bg-[#103080] flex items-center justify-center group-hover:bg-[#0c2466] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </div>

            <!-- SENATORS CARD -->
            <div @click="activeModal = 'senators'"
                class="bg-white rounded-xl p-6 h-32 relative shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h3 class="text-black font-extrabold text-[0.95rem] uppercase tracking-wide">SENATORS</h3>
                        <p class="text-black font-bold text-lg mt-1">2</p>
                    </div>
                </div>
                <div
                    class="absolute bottom-5 right-5 w-8 h-8 rounded-full bg-[#103080] flex items-center justify-center group-hover:bg-[#0c2466] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </div>
        </div>


        <!-- ============================ -->
        <!-- 1. PRESIDENT MODAL           -->
        <!-- ============================ -->
        <div x-show="activeModal === 'president'" x-transition.opacity.duration.300ms
            class="absolute inset-0 z-40 flex items-start justify-center pt-20 pointer-events-none" x-cloak>

            <div
                class="bg-white w-full max-w-[46rem] rounded-lg shadow-2xl overflow-hidden text-black mx-4 pointer-events-auto border border-gray-100 relative">

                <div class="flex items-center justify-between px-8 py-5 border-b border-gray-200 bg-white">
                    <h3 class="text-xl font-bold tracking-tight text-black uppercase">PRESIDENT</h3>
                    <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="bg-white">
                    <table class="w-full text-left table-fixed">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="pl-8 py-4 font-bold text-sm text-gray-900 w-[25%]">Name</th>
                                <th class="px-4 py-4 font-bold text-sm text-gray-900 w-[30%]">Course</th>
                                <th class="px-4 py-4 font-bold text-sm text-gray-900 w-[20%]">Year Level</th>
                                <th class="pr-8 py-4 font-bold text-sm text-gray-900 w-[15%] text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr class="border-b border-gray-100">
                                <td class="pl-8 py-4 font-medium text-gray-800">Jose Perolino</td>
                                <td class="px-4 py-4 text-gray-700">Information Technology</td>
                                <td class="px-4 py-4 text-gray-700">4th Year</td>
                                <td class="pr-8 py-4 flex items-center gap-2 justify-end">
                                    <button
                                        class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded flex items-center justify-center text-white shadow-sm transition"><svg
                                            class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button
                                        class="w-8 h-8 bg-red-100 hover:bg-red-200 rounded flex items-center justify-center text-red-500 shadow-sm transition"><svg
                                            class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-8 py-4 font-medium text-gray-800">Jahaira Ampaso</td>
                                <td class="px-4 py-4 text-gray-700">Computer Science</td>
                                <td class="px-4 py-4 text-gray-700">1st Year</td>
                                <td class="pr-8 py-4 text-right"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-8 py-5 border-t border-gray-200">
                        <div class="flex items-center gap-2 text-sm text-gray-800 font-medium">
                            <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Maximum candidates reached for this position.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ============================ -->
        <!-- 2. SENATORS MODAL            -->
        <!-- ============================ -->
        <div x-show="activeModal === 'senators'" x-transition.opacity.duration.300ms
            class="absolute inset-0 z-40 flex items-start justify-center pt-20 pointer-events-none" x-cloak>

            <div
                class="bg-white w-full max-w-[46rem] rounded-lg shadow-2xl overflow-hidden text-black mx-4 pointer-events-auto border border-gray-100 relative">

                <div class="flex items-center justify-between px-8 py-5 border-b border-gray-200 bg-white">
                    <h3 class="text-xl font-bold tracking-tight text-black uppercase">SENATORS</h3>
                    <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="bg-white">
                    <table class="w-full text-left table-fixed">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="pl-8 py-4 font-bold text-sm text-gray-900 w-[25%]">Name</th>
                                <th class="px-4 py-4 font-bold text-sm text-gray-900 w-[30%]">Course</th>
                                <th class="px-4 py-4 font-bold text-sm text-gray-900 w-[20%]">Year Level</th>
                                <th class="pr-8 py-4 font-bold text-sm text-gray-900 w-[15%] text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr class="border-b border-gray-100">
                                <td class="pl-8 py-4 font-medium text-gray-800">Jose Perolino</td>
                                <td class="px-4 py-4 text-gray-700">Information Technology</td>
                                <td class="px-4 py-4 text-gray-700">4th Year</td>
                                <td class="pr-8 py-4 flex items-center gap-2 justify-end">
                                    <button
                                        class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded flex items-center justify-center text-white shadow-sm transition"><svg
                                            class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button
                                        class="w-8 h-8 bg-red-100 hover:bg-red-200 rounded flex items-center justify-center text-red-500 shadow-sm transition"><svg
                                            class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-8 py-4 font-medium text-gray-800">Jahaira Ampaso</td>
                                <td class="px-4 py-4 text-gray-700">Computer Science</td>
                                <td class="px-4 py-4 text-gray-700">1st Year</td>
                                <td class="pr-8 py-4 text-right"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-8 py-4 border-t border-gray-200 bg-white flex justify-between items-center">
                        <span class="text-sm text-gray-800 font-medium">You may add up to 2 more candidates.</span>
                        <!-- Opens Add Candidate -->
                        <button @click="activeModal = null; showAddCandidate = true"
                            class="flex items-center gap-1.5 bg-[#0061ff] hover:bg-blue-600 text-white px-4 py-2 rounded text-xs font-semibold tracking-wide transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                            </svg>
                            Add Candidate
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- ============================ -->
        <!-- 3. ADD CANDIDATE MODAL       -->
        <!-- ============================ -->
        <div x-show="showAddCandidate" x-transition.opacity.duration.300ms
            class="absolute inset-0 z-50 flex items-start justify-center pt-6 pointer-events-none" x-cloak>

            <div
                class="bg-white w-full max-w-[28rem] rounded-xl shadow-2xl overflow-hidden text-black mx-4 pointer-events-auto border border-gray-100 relative">

                <!-- Add Candidate Header -->
                <div class="px-6 pt-5 pb-3 bg-white">
                    <h3 class="text-lg font-bold tracking-tight text-black">Add Canditates</h3>
                </div>

                <!-- Form Content -->
                <div class="px-6 pb-6 bg-white space-y-4">

                    <!-- Image Upload -->
                    <div class="flex items-center gap-4 py-1">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <!-- Placeholder Icon -->
                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="font-medium text-black">Upload Image</span>
                            <button
                                class="flex items-center justify-center gap-1.5 bg-[#0061ff] hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors w-24">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload
                            </button>
                        </div>
                    </div>

                    <div class="h-px bg-gray-200 w-full my-1"></div>

                    <!-- Fields -->
                    <div class="space-y-3 text-sm">
                        <!-- Full Name -->
                        <div class="grid grid-cols-3 gap-2 items-center">
                            <label class="col-span-1 font-medium text-black">Full Name:</label>
                            <input type="text"
                                class="col-span-2 form-input w-full rounded border px-3 py-1.5 text-black">
                        </div>

                        <!-- Course -->
                        <div class="grid grid-cols-3 gap-2 items-center">
                            <label class="col-span-1 font-medium text-black">Course:</label>
                            <select
                                class="col-span-2 form-input w-full rounded border px-3 py-1.5 text-black bg-white">
                                <option></option>
                            </select>
                        </div>

                        <!-- Year -->
                        <div class="grid grid-cols-3 gap-2 items-center">
                            <label class="col-span-1 font-medium text-black">Year:</label>
                            <select
                                class="col-span-2 form-input w-full rounded border px-3 py-1.5 text-black bg-white">
                                <option></option>
                            </select>
                        </div>

                        <!-- Political Party -->
                        <div class="grid grid-cols-3 gap-2 items-center">
                            <label class="col-span-1 font-medium text-black">Political Party:</label>
                            <input type="text"
                                class="col-span-2 form-input w-full rounded border px-3 py-1.5 text-black">
                        </div>
                    </div>

                    <div class="h-px bg-gray-200 w-full my-2"></div>

                    <!-- Platform / Agenda -->
                    <div class="space-y-1.5">
                        <label class="block font-medium text-black text-sm">Platform / Agenda</label>
                        <textarea class="w-full form-input rounded border px-3 py-2 text-black text-sm resize-none h-24"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-2">
                        <button @click="showAddCandidate = false; activeModal = 'senators'"
                            class="w-full py-2 border border-red-300 text-red-500 rounded hover:bg-red-50 font-medium text-sm transition-colors">
                            Cancel
                        </button>

                        <!-- Opens Success Modal -->
                        <button @click="showAddCandidate = false; showSuccess = true"
                            class="w-full py-2 bg-[#0061ff] hover:bg-blue-600 text-white rounded font-medium text-sm transition-colors">
                            Add Candidate
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ============================ -->
        <!-- 4. SUCCESS MODAL             -->
        <!-- ============================ -->
        <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="absolute inset-0 z-[60] flex items-start justify-center pt-24 pointer-events-none" x-cloak>

            <div
                class="bg-white w-full max-w-[20rem] rounded-xl shadow-2xl p-6 text-black mx-4 pointer-events-auto border border-gray-100 relative text-center">

                <!-- Close X -->
                <button @click="showSuccess = false"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Check Icon Circle -->
                <div
                    class="w-16 h-16 rounded-full border-[3px] border-[#00d100] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-[#00d100]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold tracking-tight text-black mb-1">Success!</h3>
                <p class="text-sm text-gray-800 font-medium">Candidate has been successfully added.</p>
            </div>
        </div>

    </div>

</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/comelecmanagecandidate.blade.php ENDPATH**/ ?>