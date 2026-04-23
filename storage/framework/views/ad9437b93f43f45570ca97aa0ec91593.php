<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts - Fingerprint Voting System</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #102864;
        }

        .bg-main-panel {
            background-color: #0C3189;
        }

        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
    </style>
</head>

<body x-data="{
    openModal: <?php echo e(session('show_add_modal') ? 'true' : 'false'); ?>,
    showDeleteModal: false,
    openEditModal: false,
    showEditPass: false
}" class="p-4 md:p-6 min-h-screen text-white flex flex-col font-sans">

    <!-- Assuming components exist dynamically. Example: -->
    <!-- <?php echo $__env->make('components.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> -->

    <!-- ============================== -->
    <!-- DELETE CONFIRMATION MODAL      -->
    <!-- ============================== -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center p-4">

        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4" @click.away="showDeleteModal = false"
            class="bg-white rounded-2xl p-8 w-full max-w-[400px] shadow-2xl relative z-10 flex flex-col items-center text-center">

            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-5">
                <svg class="w-8 h-8 text-[#c81e1e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 mb-2">Are you sure?</h3>
            <p class="text-sm text-gray-600 font-medium mb-8">Are you sure you want to delete this account?</p>

            <div class="flex gap-4 w-full justify-center">
                <button @click="showDeleteModal = false"
                    class="bg-[#ce1b26] text-white text-sm font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-red-700 transition-colors">
                    Cancel
                </button>
                <button
                    class="bg-[#1ccb14] text-white text-sm font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-green-600 transition-colors">
                    Submit
                </button>
            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!-- EDIT EXISTING ACCOUNT MODAL    -->
    <!-- ============================== -->
    <div x-show="openEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">

        <!-- Modal Card -->
        <div @click.away="openEditModal = false"
            class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl text-gray-800 relative">
            <h2 class="text-2xl font-bold mb-6 text-gray-900">Edit Account Role</h2>

            <form class="space-y-4" method="POST" action="">
                <!-- Dummy form layout -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Full Name</label>
                    <input type="text" value="Jose Perolino" disabled
                        class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:outline-none text-sm font-medium text-gray-400 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Email Address</label>
                    <input type="email" value="perolino@gmail.com" disabled
                        class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:outline-none text-sm font-medium text-gray-400 cursor-not-allowed">
                </div>

                <!-- ONLY EDITABLE SECTION -->
                <div>
                    <label class="block text-xs font-bold text-blue-600 uppercase mb-1 ml-1">Role (Editable)</label>
                    <div class="relative">
                        <select name="role"
                            class="w-full px-4 py-3 rounded-xl border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold bg-blue-50/50 appearance-none text-gray-700 cursor-pointer hover:border-blue-400 transition-all">
                            <option value="comelec" selected>Comelec</option>
                            <option value="sao">SAO Head</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- TOGGLE-VIEW PASSWORD (STRICTLY VIEWABLE COMPLIANCE ONLY) -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 flex justify-between">
                        Account Password
                        <span @click="showEditPass = !showEditPass"
                            class="text-blue-500 lowercase hover:underline cursor-pointer tracking-wider mr-2 font-semibold">
                            Toggle Visibility
                        </span>
                    </label>
                    <div class="relative">
                        <input :type="showEditPass ? 'text' : 'password'" value="mysecretpassword" readonly
                            class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:outline-none text-sm font-medium text-gray-500 cursor-not-allowed">
                        <div @click="showEditPass = !showEditPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 cursor-pointer">
                            <template x-if="!showEditPass">
                                <!-- Eye Closed Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </template>
                            <template x-if="showEditPass">
                                <!-- Eye Open Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="openEditModal = false; showEditPass = false"
                        class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-500 font-bold text-xs hover:bg-gray-50 uppercase tracking-wide transition-colors">Cancel</button>
                    <button type="button" @click="openEditModal = false; showEditPass = false"
                        class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 uppercase tracking-wide shadow-lg shadow-blue-200/50 transition-all">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ============================== -->
    <!-- ADD NEW ACCOUNT MODAL          -->
    <!-- ============================== -->
    <div x-show="openModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">

        <!-- Modal Card -->
        <div @click.away="openModal = false"
            class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl text-gray-800 relative">
            <h2 class="text-2xl font-bold mb-6 text-gray-900">Add New Account</h2>
            
            <?php if(session('error') || $errors->has('general')): ?>
                <div
                    class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-[13px] font-medium">
                    <?php echo e(session('error') ?? $errors->first('general')); ?>

                </div>
            <?php endif; ?>

            
            <?php if(session('success')): ?>
                <div
                    class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-600 text-[13px] font-medium">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <!-- Placeholder Form Route Layout - Keep mapped backend constraints if known natively: -->
            <form action="<?php echo e(route('store.new-accounts')); ?>" class="space-y-4" method="POST">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">First Name</label>
                    <input type="text" name="first_name" value="<?php echo e(old('first_name')); ?>"
                        placeholder="Enter First Name"
                        class="w-full px-4 py-3 rounded-xl border <?php echo e($errors->has('first_name') ? 'border-red-400' : 'border-gray-200'); ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                    <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Middle Name</label>
                    <input type="text" name="middle_name" value="<?php echo e(old('middle_name')); ?>"
                        placeholder="Enter Middle Name"
                        class="w-full px-4 py-3 rounded-xl border <?php echo e($errors->has('middle_name') ? 'border-red-400' : 'border-gray-200'); ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                    <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Last Name</label>
                    <input type="text" name="last_name" value="<?php echo e(old('last_name')); ?>"
                        placeholder="Enter Last Name"
                        class="w-full px-4 py-3 rounded-xl border <?php echo e($errors->has('last_name') ? 'border-red-400' : 'border-gray-200'); ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                    <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Email Address</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                        placeholder="example@gmail.com"
                        class="w-full px-4 py-3 rounded-xl border <?php echo e($errors->has('email') ? 'border-red-400' : 'border-gray-200'); ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Role</label>
                    <div class="relative">
                        <select name="role"
                            class="w-full px-4 py-3 rounded-xl border <?php echo e($errors->has('role') ? 'border-red-400' : 'border-gray-200'); ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium bg-white appearance-none">
                            <option value="">Select Role</option>
                            <option value="student" <?php echo e(old('role') === 'student' ? 'selected' : ''); ?>>Student
                            </option>
                            <option value="comelec" <?php echo e(old('role') === 'comelec' ? 'selected' : ''); ?>>Comelec
                            </option>
                            <option value="sao" <?php echo e(old('role') === 'sao' ? 'selected' : ''); ?>>SAO Head
                            </option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Password</label>
                    <input type="password" name="password" placeholder="Enter Password"
                        class="w-full px-4 py-3 rounded-xl border <?php echo e($errors->has('password') ? 'border-red-400' : 'border-gray-200'); ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="openModal = false"
                        class="flex-1 py-3 rounded-xl border border-red-400 text-red-500 font-bold text-xs hover:bg-red-50 uppercase tracking-wide transition-colors">Cancel</button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 uppercase tracking-wide shadow-lg shadow-blue-200/50 transition-all">Add
                        Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- HEADER SECTION -->
    <div class="max-w-7xl mx-auto w-full mb-5 flex items-center justify-between px-2 mt-4 md:mt-2">
        <div class="flex items-center gap-4">
            <!-- Replace standard Back URL to previous link bindings internally safely formatted. -->
            <a href="<?php echo e(route('view.quick-access')); ?>"
                class="bg-white text-[#113285] rounded-full w-10 h-10 flex items-center justify-center hover:scale-110 transition-transform shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white leading-tight">Manage Accounts</h1>
                <p class="text-blue-200 text-[11px] font-medium mt-0.5">Manage Roles</p>
            </div>
        </div>
    </div>

    <!-- MAIN BLUE CONTAINER -->
    <div
        class="max-w-7xl mx-auto w-full bg-main-panel rounded-3xl p-6 md:p-10 relative shadow-2xl flex-1 flex flex-col mb-4 overflow-hidden">

        <!-- STATS CARDS -->
        <!-- Reshaped strictly into Top 3 Elements aligned on screen layout requirements without Red block -->
        <div class="flex flex-wrap gap-5 mb-6 md:max-w-3xl relative z-10">
            <!-- 1. Total Accounts -->
            <div
                class="bg-white rounded-[20px] p-5 py-4 w-[250px] flex items-center gap-4 shadow-sm flex-1 min-w-[240px]">
                <div
                    class="bg-blue-600 w-[52px] h-[52px] rounded-[16px] flex items-center justify-center text-white shrink-0 shadow-md">
                    <!-- Replace fallback icon or bind original directory mapping visually -->
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-[32px] font-bold text-gray-900 leading-tight"><?php echo e($counts['total']); ?></div>
                    <div class="text-[12px] text-gray-500 font-semibold tracking-wide">Total Accounts</div>
                </div>
            </div>

            <!-- 2. Comelec Officers -->
            <div
                class="bg-white rounded-[20px] p-5 py-4 w-[250px] flex items-center gap-4 shadow-sm flex-1 min-w-[240px]">
                <div
                    class="bg-[#24b93b] w-[52px] h-[52px] rounded-[16px] flex items-center justify-center text-white shrink-0 shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-[32px] font-bold text-gray-900 leading-tight"><?php echo e($counts['comelec']); ?></div>
                    <div class="text-[12px] text-gray-500 font-semibold tracking-wide">Comelec Officers</div>
                </div>
            </div>

            <!-- 3. SAO Head -->
            <div
                class="bg-white rounded-[20px] p-5 py-4 w-[250px] flex items-center gap-4 shadow-sm flex-1 min-w-[240px]">
                <div
                    class="bg-[#ffd93d] w-[52px] h-[52px] rounded-[16px] flex items-center justify-center text-white shrink-0 shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-[32px] font-bold text-gray-900 leading-tight"><?php echo e($counts['sao']); ?></div>
                    <div class="text-[12px] text-gray-500 font-semibold tracking-wide">SAO Head</div>
                </div>
            </div>
            <!-- NOTE: 4. Erased red block to enforce identical match properly requested by User Guidelines (Rule "Gipa-erase delete block..") -->
        </div>

        <!-- Right Side Added Filter Panel Header Box -->
        <div class="flex justify-end relative z-10 w-full mb-3 -mt-3 pr-2 items-center min-w-max ml-auto">
            <div class="relative w-[280px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white/90">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <select
                    onchange="window.location.href = '<?php echo e(route('view.manage-accounts')); ?>?school_year=' + this.value"
                    class="block w-full py-[10px] pl-[38px] pr-10 rounded-[10px] border border-white/80 bg-[#163fa9] focus:outline-none focus:ring-2 focus:ring-white/40 appearance-none text-[13.5px] font-medium text-white shadow-sm hover:bg-white/10 transition-colors cursor-pointer">

                    <option value="" class="text-black" <?php echo e(!$schoolYearFilter ? 'selected' : ''); ?>>All Years
                    </option>
                    <option value="2023-2024" class="text-black"
                        <?php echo e($schoolYearFilter === '2023-2024' ? 'selected' : ''); ?>>
                        School Year 2023 - 2024</option>
                    <option value="2024-2025" class="text-black"
                        <?php echo e($schoolYearFilter === '2024-2025' ? 'selected' : ''); ?>>
                        School Year 2024 - 2025</option>
                    <option value="2025-2026" class="text-black"
                        <?php echo e($schoolYearFilter === '2025-2026' ? 'selected' : ''); ?>>
                        School Year 2025 - 2026</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-white z-20">
                    <svg class="w-[18px] h-[18px] stroke-[2.5]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <!-- Formats table columns padding, row spacings and exact style colors/alignments based precisely mapped -->
        <div
            class="bg-white rounded-2xl overflow-hidden shadow-xl flex-1 relative z-10 flex flex-col mb-4 max-h-[100%]">
            <div class="overflow-x-auto w-full max-h-full pb-[35px] hide-scrollbar rounded-b-2xl">
                <table class="w-full text-left border-collapse min-w-[850px] mb-8">
                    <thead>
                        <tr
                            class="border-b-2 border-gray-100 bg-white shadow-[0px_2px_8px_rgba(0,0,0,0.015)] text-left sticky top-0 z-30">
                            <th class="pl-[42px] pr-6 py-5 text-[15px] font-bold text-[#0c0d16] leading-tight">Name
                            </th>
                            <th class="px-6 py-5 text-[15px] font-bold text-[#0c0d16] leading-tight w-2/6">Email</th>
                            <th class="px-6 py-5 text-[15px] font-bold text-[#0c0d16] text-center leading-tight">Role
                            </th>
                            <th class="px-6 py-5 text-[15px] font-bold text-[#0c0d16] text-center leading-tight">
                                Created</th>
                            <th class="pl-6 pr-[42px] py-5 text-[15px] font-bold text-[#0c0d16] text-right w-[150px]">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/60 bg-white overflow-y-auto">
                        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-blue-50/20 transition-colors group">
                                <td class="pl-[42px] pr-6 py-[22px]">
                                    <div class="flex items-center gap-[18px]">
                                        <div
                                            class="w-[32px] h-[32px] rounded-full bg-[#1e4cd6] flex items-center justify-center text-white shadow-[0_2px_6px_rgba(30,76,214,0.3)] shrink-0 mt-[2px]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-[14.5px] font-medium tracking-[-0.015em] text-[#292c3a]">
                                            <?php echo e($user->getFirstName()); ?> <?php echo e($user->getLastName()); ?>

                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-[22px] text-[14.5px] text-[#2d3043] tracking-wide">
                                    <?php echo e($user->getEmail()); ?>

                                </td>
                                <td class="px-6 py-[22px] text-center">
                                    <?php if($user->getRole() === 'comelec'): ?>
                                        <span
                                            class="bg-[#d2e2fa] text-[#4f6492] text-[11px] tracking-wide font-bold px-[18px] py-[6px] rounded-full border-[0.5px] border-[#adc7f6]/40 inline-flex items-center">Comelec</span>
                                    <?php elseif($user->getRole() === 'sao'): ?>
                                        <span
                                            class="bg-[#fee173] text-[#4f4316] text-[10px] tracking-wide font-bold px-[18px] py-[6px] rounded-full border border-yellow-300 inline-flex items-center">SAO
                                            Head</span>
                                    <?php else: ?>
                                        <span
                                            class="bg-gray-100 text-gray-500 text-[11px] tracking-wide font-bold px-[18px] py-[6px] rounded-full inline-flex items-center"><?php echo e(ucfirst($user->getRole())); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-[22px] text-center text-[14.5px] font-medium text-[#44465b]">
                                    <?php echo e(\Carbon\Carbon::parse($user->getCreatedAt())->format('m-d-Y')); ?>

                                </td>
                                <td class="pl-6 pr-[42px] py-[22px]">
                                    <div class="flex items-center justify-end gap-3">
                                        <?php if($user->getRole() !== 'admin'): ?>
                                            <button @click="showDeleteModal = true"
                                                class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-md border border-gray-100 hover:bg-red-50 hover:border-red-100 group transition-all">
                                                <svg class="w-[16px] h-[16px] text-[#ced0db] group-hover:text-red-500 transition-colors"
                                                    fill="none" stroke="currentColor" stroke-width="2.2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                        <button @click="openEditModal = true; showEditPass = false"
                                            class="w-8 h-8 flex items-center justify-center bg-[#1853fc] hover:bg-[#123ebd] hover:-translate-y-px rounded-md text-white shadow-[0_2px_8px_rgba(24,83,252,0.4)] transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-16 text-gray-400 font-medium">No accounts
                                    found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if($data->lastPage() > 1): ?>
                    <div
                        class="flex items-center justify-between px-[42px] py-4 border-t border-gray-100 bg-white sticky bottom-0">
                        <p class="text-[13px] text-gray-400 font-medium">
                            Showing <?php echo e($data->firstItem()); ?>–<?php echo e($data->lastItem()); ?> of <?php echo e($data->total()); ?> accounts
                        </p>
                        <div class="flex items-center gap-2">

                            
                            <?php if($data->onFirstPage()): ?>
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </span>
                            <?php else: ?>
                                <a href="<?php echo e($data->previousPageUrl()); ?>"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            
                            <?php
                                $current = $data->currentPage();
                                $last = $data->lastPage();
                                $start = max(1, min($current - 2, $last - 4));
                                $end = min($last, $start + 4);
                            ?>

                            
                            <?php if($start > 1): ?>
                                <a href="<?php echo e($data->url(1)); ?>"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-[13px] font-medium hover:bg-blue-50 hover:text-blue-600 transition-all">1</a>
                                <?php if($start > 2): ?>
                                    <span
                                        class="w-8 h-8 flex items-center justify-center text-gray-400 text-[13px]">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            
                            <?php for($page = $start; $page <= $end; $page++): ?>
                                <?php if($page == $current): ?>
                                    <span
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#1853fc] text-white text-[13px] font-bold shadow"><?php echo e($page); ?></span>
                                <?php else: ?>
                                    <a href="<?php echo e($data->url($page)); ?>"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-[13px] font-medium hover:bg-blue-50 hover:text-blue-600 transition-all"><?php echo e($page); ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            
                            <?php if($end < $last): ?>
                                <?php if($end < $last - 1): ?>
                                    <span
                                        class="w-8 h-8 flex items-center justify-center text-gray-400 text-[13px]">...</span>
                                <?php endif; ?>
                                <a href="<?php echo e($data->url($last)); ?>"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-[13px] font-medium hover:bg-blue-50 hover:text-blue-600 transition-all"><?php echo e($last); ?></a>
                            <?php endif; ?>

                            
                            <?php if($data->hasMorePages()): ?>
                                <a href="<?php echo e($data->nextPageUrl()); ?>"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            <?php else: ?>
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FLOATING ADD BUTTON BOUND INTERNALLY -->
        <!-- Fixed rule modification from requirements (ang add account button kay naasulod): changed "fixed bottom right outside -> absolute constrained block inner corner" AND replaced plain Add mapping cross shape natively  -->
        <button @click="openModal = true"
            class="absolute bottom-5 right-7 md:bottom-7 md:right-8 bg-[#0b64f9] w-14 h-14 rounded-full flex items-center justify-center text-white shadow-[0_5px_22px_rgba(5,20,80,0.6)] border-2 border-[#549bf7]/30 hover:bg-blue-600 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 z-50">
            <svg class="w-[26px] h-[26px]" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <!-- Custom SVG Person-Plus Exact Match Visually from Request -->
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </button>
    </div>

</body>

</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/manage-accounts.blade.php ENDPATH**/ ?>