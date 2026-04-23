\@extends('components.admin-layout')
@section('title', 'Manage Accounts — MCC Voting System')
@section('page-title', 'Manage Accounts')
@section('page-sub', 'Create and manage user roles')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{
    openModal: {{ session('show_add_modal') ? 'true' : 'false' }},
    showDeleteModal: false,
    deleteUserId: null,
    deleteUserName: '',
    openEditModal: false,
    showEditPass: false,
    editUser: {},
    roleFilter: 'all',
    get filteredCount() {
        if (this.roleFilter === 'all') return {{ $data->total() }};
        if (this.roleFilter === 'comelec') return {{ $counts['comelec'] }};
        if (this.roleFilter === 'sao') return {{ $counts['sao'] }};
        if (this.roleFilter === 'admin') return {{ $counts['admin'] ?? 0 }};
        return 0;
    },
    get showPagination() {
        return this.roleFilter === 'all' && {{ $data->lastPage() }} > 1;
    },
    filterByRole(role) {
        this.roleFilter = role;
        document.querySelector('.accounts-table')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}" class="font-sans">

    <!-- ============================== -->
    <!-- DELETE CONFIRMATION MODAL      -->
    <!-- ============================== -->
    <div x-show="showDeleteModal" 
         x-cloak 
         class="fixed inset-0 z-[150] flex items-center justify-center p-4">

        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0" 
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4" 
             @click.away="showDeleteModal = false"
             class="bg-white rounded-2xl p-8 w-full max-w-[400px] shadow-2xl relative z-10 flex flex-col items-center text-center">

            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-5">
                <svg class="w-8 h-8 text-[#c81e1e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 mb-2">Are you sure?</h3>
            <p class="text-sm text-gray-600 font-medium mb-8">
                Delete account for <strong x-text="deleteUserName"></strong>? This cannot be undone.
            </p>

            <div class="flex gap-4 w-full justify-center">
                <button @click="showDeleteModal = false"
                    class="bg-[#ce1b26] text-white text-sm font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-red-700 transition-colors">
                    Cancel
                </button>
                <form :action="'{{ url('delete-account') }}/' + deleteUserId" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-[#1ccb14] text-white text-sm font-bold py-2.5 px-8 rounded-lg shadow-md hover:bg-green-600 transition-colors">
                        Confirm Delete
                    </button>
                </form>
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

        <div @click.away="openEditModal = false"
            class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl text-gray-800 relative">
            <h2 class="text-2xl font-bold mb-6 text-gray-900">Edit Account</h2>

            <form class="space-y-4" method="POST" action="{{ route('account.update') }}">
                @csrf
                <input type="hidden" name="user_id" :value="editUser.id">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">First Name</label>
                        <input type="text" name="first_name" :value="editUser.first_name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Last Name</label>
                        <input type="text" name="last_name" :value="editUser.last_name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Middle Name</label>
                    <input type="text" name="middle_name" :value="editUser.middle_name"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Email Address</label>
                    <input type="email" name="email" :value="editUser.email"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase mb-1 ml-1">Role</label>
                    <div class="relative">
                        <select name="role"
                            class="w-full px-4 py-3 rounded-xl border border-blue-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-semibold bg-blue-50/50 appearance-none text-gray-700 cursor-pointer hover:border-blue-400 transition-all">
                            <option value="student"  :selected="editUser.role === 'student'">Student</option>
                            <option value="comelec"  :selected="editUser.role === 'comelec'">Comelec</option>
                            <option value="sao"      :selected="editUser.role === 'sao'">SAO Head</option>
                            <option value="admin"    :selected="editUser.role === 'admin'">Admin</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 flex justify-between">
                        New Password
                        <span class="text-gray-400 lowercase font-normal">(leave blank to keep current)</span>
                    </label>
                    <div class="relative">
                        <input :type="showEditPass ? 'text' : 'password'" name="password"
                            placeholder="Enter new password (optional)"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium pr-12">
                        <button type="button" @click="showEditPass = !showEditPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-700">
                            <svg x-show="!showEditPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg x-show="showEditPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="openEditModal = false; showEditPass = false"
                        class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-500 font-bold text-xs hover:bg-gray-50 uppercase tracking-wide transition-colors">Cancel</button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-green-700 text-white font-bold text-xs hover:bg-green-800 uppercase tracking-wide shadow-lg transition-all">Save Changes</button>
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
            {{-- General error banner --}}
            @if (session('error') || $errors->has('general'))
                <div
                    class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-[13px] font-medium">
                    {{ session('error') ?? $errors->first('general') }}
                </div>
            @endif

            {{-- Success banner --}}
            @if (session('success'))
                <div
                    class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-600 text-[13px] font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Placeholder Form Route Layout - Keep mapped backend constraints if known natively: -->
            <form action="{{ route('store.new-accounts') }}" class="space-y-4" method="POST">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                        placeholder="Enter First Name"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium">
                    @error('first_name')
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                        placeholder="Enter Middle Name"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('middle_name') ? 'border-red-400' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium">
                    @error('middle_name')
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                        placeholder="Enter Last Name"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium">
                    @error('last_name')
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="example@gmail.com"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium">
                    @error('email')
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Role</label>
                    <div class="relative">
                        <select name="role"
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('role') ? 'border-red-400' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium bg-white appearance-none">
                            <option value="">Select Role</option>
                            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student
                            </option>
                            <option value="comelec" {{ old('role') === 'comelec' ? 'selected' : '' }}>Comelec
                            </option>
                            <option value="sao" {{ old('role') === 'sao' ? 'selected' : '' }}>SAO Head
                            </option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('role')
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Password</label>
                    <input type="password" name="password" placeholder="Enter Password"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium">
                    @error('password')
                        <p class="text-red-500 text-[11px] mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="openModal = false"
                        class="flex-1 py-3 rounded-xl border border-red-400 text-red-500 font-bold text-xs hover:bg-red-50 uppercase tracking-wide transition-colors">Cancel</button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-green-700 text-white font-bold text-xs hover:bg-green-800 uppercase tracking-wide shadow-lg shadow-blue-200/50 transition-all">Add
                        Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        <div @click="filterByRole('all')" 
             :class="roleFilter === 'all' ? 'ring-2 ring-[#1a5c38] bg-green-50' : ''"
             class="bg-white border border-gray-100 rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-gray-900">{{ $counts['total'] }}</div>
                <div class="text-xs font-semibold text-gray-500 mt-0.5">Total Accounts</div>
            </div>
        </div>
        <div @click="filterByRole('comelec')" 
             :class="roleFilter === 'comelec' ? 'ring-2 ring-green-500 bg-green-50' : ''"
             class="bg-white border border-gray-100 rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-green-100">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-gray-900">{{ $counts['comelec'] }}</div>
                <div class="text-xs font-semibold text-gray-500 mt-0.5">Comelec Officers</div>
            </div>
        </div>
        <div @click="filterByRole('sao')" 
             :class="roleFilter === 'sao' ? 'ring-2 ring-amber-400 bg-amber-50' : ''"
             class="bg-white border border-gray-100 rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-amber-100">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-gray-900">{{ $counts['sao'] }}</div>
                <div class="text-xs font-semibold text-gray-500 mt-0.5">SAO Head</div>
            </div>
        </div>
    </div>

    <!-- Year Filter row -->
    <div class="flex items-center justify-between mb-4">
        <div class="relative">
            <select onchange="window.location.href = '{{ route('view.manage-accounts') }}?school_year=' + this.value"
                    class="pl-9 pr-8 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                <option value="" {{ !$schoolYearFilter ? 'selected' : '' }}>All Years</option>
                <option value="2023-2024" {{ $schoolYearFilter === '2023-2024' ? 'selected' : '' }}>2023 - 2024</option>
                <option value="2024-2025" {{ $schoolYearFilter === '2024-2025' ? 'selected' : '' }}>2024 - 2025</option>
                <option value="2025-2026" {{ $schoolYearFilter === '2025-2026' ? 'selected' : '' }}>2025 - 2026</option>
            </select>
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>

    <!-- TABLE SECTION -->
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[850px]">
                    <thead>
                        <tr style="background:#1a5c38;">
                            <th class="pl-[42px] pr-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest">Name</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest w-2/6">Email</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-center">Role</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-white uppercase tracking-widest text-center">Created</th>
                            <th class="pl-6 pr-[42px] py-4 text-[11px] font-bold text-white uppercase tracking-widest text-right w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white accounts-table">
                        @forelse ($data as $user)
                            <tr class="hover:bg-green-50/30 transition-colors group"
                                x-show="roleFilter === 'all' || roleFilter === '{{ $user->getRole() }}'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                style="display: table-row;"
                                :style="(roleFilter === 'all' || roleFilter === '{{ $user->getRole() }}') ? 'display:table-row' : 'display:none'">
                                <td class="pl-[42px] pr-6 py-[18px]">
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
                                            {{ $user->getFirstName() }} {{ $user->getLastName() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-[22px] text-[14.5px] text-[#2d3043] tracking-wide">
                                    {{ $user->getEmail() }}
                                </td>
                                <td class="px-6 py-[22px] text-center">
                                    @if ($user->getRole() === 'comelec')
                                        <span
                                            class="bg-[#d2e2fa] text-[#4f6492] text-[11px] tracking-wide font-bold px-[18px] py-[6px] rounded-full border-[0.5px] border-[#adc7f6]/40 inline-flex items-center">Comelec</span>
                                    @elseif ($user->getRole() === 'sao')
                                        <span
                                            class="bg-[#fee173] text-[#4f4316] text-[10px] tracking-wide font-bold px-[18px] py-[6px] rounded-full border border-yellow-300 inline-flex items-center">SAO
                                            Head</span>
                                    @else
                                        <span
                                            class="bg-gray-100 text-gray-500 text-[11px] tracking-wide font-bold px-[18px] py-[6px] rounded-full inline-flex items-center">{{ ucfirst($user->getRole()) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-[22px] text-center text-[14.5px] font-medium text-[#44465b]">
                                    {{ \Carbon\Carbon::parse($user->getCreatedAt())->format('m-d-Y') }}
                                </td>
                                <td class="pl-6 pr-[42px] py-[22px]">
                                    <div class="flex items-center justify-end gap-3">
                                        @if ($user->getRole() !== 'admin')
                                            <button @click="deleteUserId = '{{ $user->getId() }}'; deleteUserName = '{{ addslashes($user->getFirstName() . ' ' . $user->getLastName()) }}'; showDeleteModal = true"
                                                class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-md border border-gray-100 hover:bg-red-50 hover:border-red-100 group transition-all">
                                                <svg class="w-[16px] h-[16px] text-[#ced0db] group-hover:text-red-500 transition-colors"
                                                    fill="none" stroke="currentColor" stroke-width="2.2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button @click="editUser = {
                                                id: '{{ $user->getId() }}',
                                                first_name: '{{ addslashes($user->getFirstName()) }}',
                                                middle_name: '{{ addslashes($user->getMiddleName()) }}',
                                                last_name: '{{ addslashes($user->getLastName()) }}',
                                                email: '{{ $user->getEmail() }}',
                                                role: '{{ $user->getRole() }}'
                                            }; openEditModal = true; showEditPass = false"
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
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-16 text-gray-400 font-medium">No accounts
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Pagination for "All" filter -->
                <div x-show="showPagination" x-cloak
                    class="flex items-center justify-between px-[42px] py-4 border-t border-gray-100 bg-white sticky bottom-0">
                    <p class="text-[13px] text-gray-400 font-medium">
                        Showing {{ $data->firstItem() }}–{{ $data->lastItem() }} of {{ $data->total() }} accounts
                    </p>
                    <div class="flex items-center gap-2">

                            {{-- Previous --}}
                            @if ($data->onFirstPage())
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $data->previousPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-green-700 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            @endif

                            {{-- 5 page buttons centered around current page --}}
                            @php
                                $current = $data->currentPage();
                                $last = $data->lastPage();
                                $start = max(1, min($current - 2, $last - 4));
                                $end = min($last, $start + 4);
                            @endphp

                            {{-- Leading ellipsis --}}
                            @if ($start > 1)
                                <a href="{{ $data->url(1) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-[13px] font-medium hover:bg-blue-50 hover:text-green-700 transition-all">1</a>
                                @if ($start > 2)
                                    <span
                                        class="w-8 h-8 flex items-center justify-center text-gray-400 text-[13px]">...</span>
                                @endif
                            @endif

                            {{-- Page window --}}
                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $current)
                                    <span
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#1853fc] text-white text-[13px] font-bold shadow">{{ $page }}</span>
                                @else
                                    <a href="{{ $data->url($page) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-[13px] font-medium hover:bg-blue-50 hover:text-green-700 transition-all">{{ $page }}</a>
                                @endif
                            @endfor

                            {{-- Trailing ellipsis --}}
                            @if ($end < $last)
                                @if ($end < $last - 1)
                                    <span
                                        class="w-8 h-8 flex items-center justify-center text-gray-400 text-[13px]">...</span>
                                @endif
                                <a href="{{ $data->url($last) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-[13px] font-medium hover:bg-blue-50 hover:text-green-700 transition-all">{{ $last }}</a>
                            @endif

                            {{-- Next --}}
                            @if ($data->hasMorePages())
                                <a href="{{ $data->nextPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-green-700 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            @endif

                        </div>
                    </div>
                
                <!-- Filtered results message (for COMELEC, SAO, etc.) -->
                <div x-show="!showPagination && roleFilter !== 'all'" x-cloak
                    class="flex items-center justify-between px-[42px] py-4 border-t border-gray-100 bg-white sticky bottom-0">
                    <p class="text-[13px] text-gray-400 font-medium">
                        <span x-text="'Showing ' + filteredCount + ' of ' + filteredCount + ' accounts'"></span>
                        <span x-show="roleFilter === 'comelec'" class="ml-2 text-green-600 font-semibold">(COMELEC Officers)</span>
                        <span x-show="roleFilter === 'sao'" class="ml-2 text-yellow-600 font-semibold">(SAO Head)</span>
                        <span x-show="roleFilter === 'admin'" class="ml-2 text-green-700 font-semibold">(Admin)</span>
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#1853fc] text-white text-[13px] font-bold shadow">1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING ADD BUTTON -->
        <button @click="openModal = true"
            class="absolute bottom-5 right-7 md:bottom-7 md:right-8 bg-[#1a5c38] w-14 h-14 rounded-full flex items-center justify-center text-white shadow-[0_5px_22px_rgba(26,92,56,0.6)] border-2 border-[#2d7a52]/30 hover:bg-[#2d7a52] hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 z-50">
            <svg class="w-[26px] h-[26px]" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </button>
    </div>

    <script>
        // Force reload from server if page was loaded from cache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                // Page was loaded from cache, force reload
                window.location.reload();
            }
        });
        
        // Add timestamp to prevent caching
        if (performance.navigation.type === performance.navigation.TYPE_BACK_FORWARD) {
            window.location.reload();
        }
    </script>

</div>{{-- end x-data --}}

@endsection

