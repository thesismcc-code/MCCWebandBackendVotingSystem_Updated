@extends('components.admin-layout')
@section('title', 'Fingerprint Enrollment — MCC Voting System')
@section('page-title', 'Fingerprint Enrollment')
@section('page-sub', 'Register new students and capture biometric data')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak] { display: none !important; }
    .stat-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); transition: transform .2s, box-shadow .2s; border: 1px solid #e8ede3; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.08); }
    .badge-enrolled { background:#dcfce7; color:#15803d; padding:4px 12px; border-radius:20px; font-size:11.5px; font-weight:700; white-space:nowrap; display:inline-flex; align-items:center; gap:5px; }
    .badge-not { background:#fee2e2; color:#b91c1c; padding:4px 12px; border-radius:20px; font-size:11.5px; font-weight:700; white-space:nowrap; display:inline-flex; align-items:center; gap:5px; }
    .modal-bg { background:rgba(0,0,0,.55); backdrop-filter:blur(4px); }
    select, input[type=text] { background-color: white; }
    @keyframes scan-move { 0%, 100% { top: 10%; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 50% { top: 85%; } }
    .scan-animation { animation: scan-move 2s infinite ease-in-out; box-shadow: 0 0 8px #22c504; position: absolute; }
</style>
<div x-data="{
        search: '',
        yearFilter: '',
        courseFilter: '',
        cardFilter: '',   // 'all' | 'enrolled' | 'not_enrolled' | 'today'
        openModal: false,
        openEditModal: false,
        openAddModal: false,
        step: 1,
        isScanning: false,
        captureCount: 0,
        captures: [],
        selectedUser: null,
        editUser: {},
        addUser: { first_name:'', last_name:'', middle_name:'', email:'', password:'', course:'Information Technology', year_level:'1st Year' },
        addErrors: {},
        editErrors: {},
        isSaving: false,
        scanMessage: '',
        scanError: '',
        deviceReady: false,
        statusTimer: null,
        notify: { show:false, type:'success', title:'', message:'' },

        students: {{ Js::from($students) }},
        enrolledCount: {{ $enrolledCount }},
        enrolledToday: {{ $enrolledToday }},
        totalStudents: {{ $totalStudents }},

        currentPage: 1,
        perPage: 10,

        get filtered() {
            const today = new Date().toLocaleDateString('en-US', {month:'2-digit',day:'2-digit',year:'numeric'}).replace(/\//g,'.');
            let list = this.students;
            if (this.cardFilter === 'enrolled')     list = list.filter(s => s.enrolled);
            if (this.cardFilter === 'not_enrolled') list = list.filter(s => !s.enrolled);
            if (this.cardFilter === 'today')        list = list.filter(s => s.enrolled && s.enrolled_at === today);
            if (this.search) {
                const q = this.search.toLowerCase();
                list = list.filter(s => s.name.toLowerCase().includes(q) || s.student_id.toLowerCase().includes(q) || (s.email||'').toLowerCase().includes(q));
            }
            if (this.yearFilter)   list = list.filter(s => s.year_level === this.yearFilter);
            if (this.courseFilter) list = list.filter(s => s.course === this.courseFilter);
            return list;
        },

        get paginated() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },

        get pageNumbers() {
            const pages = [];
            for (let i = 1; i <= Math.min(this.totalPages, 5); i++) pages.push(i);
            return pages;
        },

        csrf() { return document.querySelector('meta[name=csrf-token]').content; },

        toast(type, title, msg) {
            this.notify = { show:true, type, title, message:msg };
            setTimeout(() => this.notify.show = false, 4000);
        },

        openEdit(student) {
            this.editUser = { ...student };
            this.editErrors = {};
            this.openEditModal = true;
        },

        async saveEdit() {
            this.editErrors = {};
            if (!this.editUser.first_name?.trim()) { this.editErrors.first_name = 'Required'; return; }
            if (!this.editUser.last_name?.trim())  { this.editErrors.last_name  = 'Required'; return; }
            if (!this.editUser.email?.trim())       { this.editErrors.email      = 'Required'; return; }

            this.isSaving = true;
            try {
                const body = {
                    id:          this.editUser.id,
                    first_name:  this.editUser.first_name.trim(),
                    last_name:   this.editUser.last_name.trim(),
                    middle_name: this.editUser.middle_name?.trim() ?? '',
                    email:       this.editUser.email.trim(),
                    course:      this.editUser.course,
                    year_level:  this.editUser.year_level,
                };
                if (this.editUser.password?.trim()) body.password = this.editUser.password;

                const r = await fetch('/fingerprint/student/update', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const d = await r.json();
                if (r.ok && d.success) {
                    // Update ALL fields in the table row immediately — no refresh needed
                    const idx = this.students.findIndex(s => s.id === this.editUser.id);
                    if (idx !== -1) {
                        this.students[idx].first_name  = body.first_name;
                        this.students[idx].last_name   = body.last_name;
                        this.students[idx].middle_name = body.middle_name;
                        this.students[idx].name        = body.first_name + ' ' + body.last_name;
                        this.students[idx].email       = body.email;
                        this.students[idx].course      = body.course;
                        this.students[idx].year_level  = body.year_level;
                    }
                    this.openEditModal = false;
                    this.editUser = {};
                    this.toast('success', 'Updated!', body.first_name + ' ' + body.last_name + ' has been updated successfully.');
                } else {
                    this.toast('error', 'Failed', d.message || 'Could not update student.');
                }
            } catch(e) {
                this.toast('error', 'Error', 'Could not save changes.');
            }
            this.isSaving = false;
        },

        async saveAdd() {
            this.addErrors = {};
            if (!this.addUser.first_name?.trim()) { this.addErrors.first_name = 'Required'; return; }
            if (!this.addUser.last_name?.trim())  { this.addErrors.last_name  = 'Required'; return; }
            if (!this.addUser.email?.trim())       { this.addErrors.email      = 'Required'; return; }
            if (!this.addUser.password?.trim())    { this.addErrors.password   = 'Required'; return; }

            this.isSaving = true;
            try {
                const r = await fetch('/fingerprint/student/add', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.addUser)
                });
                const d = await r.json();
                if (r.ok && d.success) {
                    this.students.unshift(d.student);
                    this.totalStudents++;
                    this.openAddModal = false;
                    this.addUser = { first_name:'', last_name:'', middle_name:'', email:'', password:'', course:'Information Technology', year_level:'1st Year' };
                    this.toast('success', 'Added!', d.student.name + ' has been added successfully.');
                } else {
                    this.toast('error', 'Failed', d.message || 'Could not add student.');
                }
            } catch(e) {
                this.toast('error', 'Error', e.message || 'Could not add student. Check console for details.');
            }
            this.isSaving = false;
        },

        openEnroll(student) {
            this.selectedUser = { ...student };
            this.captureCount = 0;
            this.captures = [];
            this.isScanning = false;
            this.step = 1;
            this.openModal = true;
        },

        async handleScan() {
            this.isScanning = true;
            this.captureCount = 0;
            this.captures = [];
            this.scanMessage = 'Place your finger on the scanner...';
            this.scanError = '';

            const MAX_ATTEMPTS = 30; // max polls per scan step

            for (let scanStep = 1; scanStep <= 3; scanStep++) {
                this.scanMessage = `Scan ${scanStep} of 3 — Place your finger on the scanner`;
                this.scanError = '';

                let captured = false;
                let attempts = 0;

                // Poll until finger is detected (max MAX_ATTEMPTS × 600ms = 18s per step)
                while (!captured && attempts < MAX_ATTEMPTS) {
                    attempts++;
                    try {
                        const r = await fetch('/api/fingerprint/capture', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' }
                        });
                        const d = await r.json();

                        if (d.captured && d.template) {
                            // ── Step 1 only: check for duplicate fingerprint ──────────
                            if (scanStep === 1) {
                                try {
                                    const idR = await fetch('/api/fingerprint/identify', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ template: d.template })
                                    });
                                    const idD = await idR.json();

                                    if (idD.matched && idD.finger_id > 0) {
                                        this.scanError = '⛔ This fingerprint is already registered in the system! Each student must use a unique finger.';
                                        this.scanMessage = '';
                                        this.isScanning = false;
                                        return;
                                    }
                                } catch(e) {
                                    // If identify fails, skip duplicate check and continue
                                }
                            }

                            // Validate consistency: compare with first scan
                            if (scanStep > 1) {
                                const matchR = await fetch('/api/fingerprint/match', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ template1: this.captures[0], template2: d.template })
                                });
                                const matchD = await matchR.json();

                                if (!matchD.matched) {
                                    this.scanError = '⚠️ Different finger detected! Please use the same finger for all 3 scans.';
                                    this.scanMessage = `Scan ${scanStep} of 3 — Try again with the SAME finger`;
                                    // Wait and retry this step
                                    await new Promise(res => setTimeout(res, 2000));
                                    attempts = 0; // reset attempts for retry
                                    continue;
                                }
                            }

                            this.captures.push(d.template);
                            this.captureCount = scanStep;
                            captured = true;
                            this.scanError = '';

                            if (scanStep < 3) {
                                this.scanMessage = `✓ Scan ${scanStep} captured! Lift your finger...`;
                                await new Promise(res => setTimeout(res, 1500));
                            }
                        } else {
                            // No finger yet — wait and retry
                            await new Promise(res => setTimeout(res, 600));
                        }
                    } catch(e) {
                        this.isScanning = false;
                        this.scanMessage = '';
                        this.toast('error', 'Error', 'Scanner not responding. Check the device.');
                        return;
                    }
                }

                if (!captured) {
                    this.isScanning = false;
                    this.scanMessage = '';
                    this.toast('error', 'Timeout', `Scan ${scanStep} timed out. Please try again.`);
                    return;
                }
            }

            // All 3 scans captured — register
            this.scanMessage = 'Processing... Please wait.';
            try {
                const r = await fetch('/api/fingerprint/register', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: this.selectedUser.id, templates: this.captures })
                });
                const d = await r.json();
                if (r.ok && d.status === 'registered') {
                    const idx = this.students.findIndex(s => s.id === this.selectedUser.id);
                    if (idx !== -1) {
                        this.students[idx].enrolled = true;
                        this.students[idx].status = 'Enrolled';
                        this.enrolledCount++;
                        this.enrolledToday++;
                    }
                    this.isScanning = false;
                    this.openModal = false;
                    setTimeout(() => { this.step = 1; this.scanMessage = ''; this.scanError = ''; }, 500);
                    this.toast('success', 'Success!', this.selectedUser.name + ' fingerprint has been successfully registered.');
                } else {
                    this.isScanning = false;
                    this.scanMessage = '';
                    this.toast('error', 'Failed!', d.detail || 'Unable to register fingerprint. Please try again.');
                }
            } catch(e) {
                this.isScanning = false;
                this.scanMessage = '';
                this.toast('error', 'Failed!', 'Unable to save fingerprint. Please try again.');
            }
        },

        async proceedFingerprint() {
            if (!this.selectedUser) return;
            this.step = 2;
        },

        async captureLoop() {
            while (this.captures.length < 3 && this.isScanning) {
                try {
                    const r = await fetch('/api/fingerprint/capture', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' }
                    });
                    const d = await r.json();
                    if (d.captured && d.template) {
                        this.captures.push(d.template);
                        this.captureCount = this.captures.length;
                        if (this.captures.length < 3) {
                            this.toast('success', 'Scan ' + this.captureCount + '/3', 'Good! Place finger again.');
                            await new Promise(res => setTimeout(res, 1500));
                        }
                    } else {
                        this.toast('error', 'No Scan', d.detail || 'Place finger on scanner.');
                        await new Promise(res => setTimeout(res, 1000));
                    }
                } catch(e) {
                    this.toast('error', 'Error', 'Scanner not responding.');
                    this.isScanning = false;
                    return;
                }
            }
            if (this.captures.length === 3) {
                await this.registerFingerprint();
            }
        },

        async registerFingerprint() {
            try {
                const r = await fetch('/api/fingerprint/register', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: this.selectedUser.id, templates: this.captures })
                });
                const d = await r.json();
                if (r.ok && d.status === 'registered') {
                    const idx = this.students.findIndex(s => s.id === this.selectedUser.id);
                    if (idx !== -1) {
                        this.students[idx].enrolled = true;
                        this.students[idx].status = 'Enrolled';
                        this.enrolledCount++;
                        this.enrolledToday++;
                    }
                    this.openModal = false;
                    this.isScanning = false;
                    this.toast('success', 'Enrolled!', this.selectedUser.name + ' fingerprint registered.');
                } else {
                    this.toast('error', 'Failed', d.detail || 'Registration failed.');
                    this.isScanning = false;
                }
            } catch(e) {
                this.toast('error', 'Error', 'Could not save fingerprint.');
                this.isScanning = false;
            }
        },

        async deleteFingerprint(student) {
            if (!confirm('Remove fingerprint for ' + student.name + '?')) return;
            try {
                await fetch('/api/fingerprint/user/' + student.id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrf() }
                });
                const idx = this.students.findIndex(s => s.id === student.id);
                if (idx !== -1) {
                    this.students[idx].enrolled = false;
                    this.students[idx].status = 'Not Enrolled';
                    this.enrolledCount = Math.max(0, this.enrolledCount - 1);
                }
                this.toast('success', 'Removed', 'Fingerprint deleted.');
            } catch(e) {
                this.toast('error', 'Error', 'Could not delete fingerprint.');
            }
        },

        async checkDevice() {
            try {
                const r = await fetch('/api/fingerprint/status');
                const d = await r.json();
                this.deviceReady = d.initialized === true;
            } catch(e) {
                this.deviceReady = false;
            }
        },

        init() {
            this.checkDevice();
            this.statusTimer = setInterval(() => this.checkDevice(), 5000);
            // Clear SDK in-memory DB to match current SQLite state
            fetch('/api/fingerprint/load-templates', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' },
                body: JSON.stringify({ members: [] })
            }).catch(() => {});
        }
    }"
    x-init="init()"
>

{{-- Centered Notification Modal --}}
<template x-teleport="body">
<div x-show="notify.show" x-cloak
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[300]"
     style="background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);">
    {{-- Inner flex centering wrapper - not controlled by x-show --}}
    <div class="w-full h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-[20px] shadow-2xl px-10 py-10 flex flex-col items-center text-center w-[360px] relative">
            <button @click="notify.show = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <template x-if="notify.type === 'success'">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 rounded-full border-[6px] border-green-500 flex items-center justify-center mb-5">
                        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-2" x-text="notify.title"></h3>
                    <p class="text-sm font-medium text-gray-500 leading-relaxed" x-text="notify.message"></p>
                </div>
            </template>
            <template x-if="notify.type === 'error'">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 rounded-full border-[6px] border-red-500 flex items-center justify-center mb-5">
                        <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-2" x-text="notify.title"></h3>
                    <p class="text-sm font-medium text-gray-500 leading-relaxed" x-text="notify.message"></p>
                </div>
            </template>
            <button @click="notify.show = false"
                    class="mt-6 px-8 py-2.5 bg-green-700 hover:bg-green-800 text-white font-bold rounded-xl text-sm transition">
                OK
            </button>
        </div>
    </div>
</div>
</template>

{{-- Device status badge --}}
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-xl px-4 py-2 shadow-sm">
        <span class="w-2.5 h-2.5 rounded-full" :class="deviceReady ? 'bg-green-400 animate-pulse' : 'bg-red-400'"></span>
        <span class="text-sm font-semibold text-gray-700" x-text="deviceReady ? 'Scanner Online' : 'Scanner Offline'"></span>
    </div>
</div>

{{-- Stat Cards --}}
<div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Total Students --}}
    <div @click="cardFilter = cardFilter === 'all' ? '' : 'all'; currentPage = 1"
         class="stat-card flex items-center gap-4 cursor-pointer"
         :class="cardFilter === 'all' ? 'ring-2 ring-green-500 shadow-lg' : ''">
        <div class="bg-green-600 w-[52px] h-[52px] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <div class="text-[30px] font-extrabold text-gray-900 leading-tight" x-text="totalStudents"></div>
            <div class="text-xs text-gray-500 font-semibold mt-0.5">Total Students</div>
        </div>
    </div>

    {{-- Enrolled Students --}}
    <div @click="cardFilter = cardFilter === 'enrolled' ? '' : 'enrolled'; currentPage = 1"
         class="stat-card flex items-center gap-4 cursor-pointer"
         :class="cardFilter === 'enrolled' ? 'ring-2 ring-green-500 shadow-lg' : ''">
        <div class="bg-green-500 w-[52px] h-[52px] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="text-[30px] font-extrabold text-gray-900 leading-tight" x-text="enrolledCount"></div>
            <div class="text-xs text-gray-500 font-semibold mt-0.5">Enrolled</div>
        </div>
    </div>

    {{-- Not Enrolled --}}
    <div @click="cardFilter = cardFilter === 'not_enrolled' ? '' : 'not_enrolled'; currentPage = 1"
         class="stat-card flex items-center gap-4 cursor-pointer"
         :class="cardFilter === 'not_enrolled' ? 'ring-2 ring-red-400 shadow-lg' : ''">
        <div class="bg-red-400 w-[52px] h-[52px] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div>
            <div class="text-[30px] font-extrabold text-gray-900 leading-tight" x-text="totalStudents - enrolledCount"></div>
            <div class="text-xs text-gray-500 font-semibold mt-0.5">Not Enrolled</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="max-w-7xl mx-auto bg-[#1a5c38]/70 rounded-2xl px-5 py-3.5 mb-4 flex flex-wrap gap-3 items-center border border-white/10">
    <div class="relative flex-1 min-w-48">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" x-model="search" @input="currentPage=1"
               placeholder="Search by Student ID or Name"
               autocomplete="off"
               class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm border-0 bg-white/10 text-white placeholder-white/50 focus:ring-2 focus:ring-green-400 focus:bg-white/15 outline-none transition">
    </div>

    <div class="relative min-w-44">
        <select x-model="yearFilter" @change="currentPage=1"
                class="w-full pl-4 pr-8 py-2.5 rounded-xl text-sm border-0 bg-white/10 text-white focus:ring-2 focus:ring-green-400 appearance-none outline-none transition">
            <option value="" class="text-gray-800">All Year Levels</option>
            <option class="text-gray-800">1st Year</option>
            <option class="text-gray-800">2nd Year</option>
            <option class="text-gray-800">3rd Year</option>
            <option class="text-gray-800">4th Year</option>
        </select>
        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/50 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>

    <div class="relative min-w-52">
        <select x-model="courseFilter" @change="currentPage=1"
                class="w-full pl-4 pr-8 py-2.5 rounded-xl text-sm border-0 bg-white/10 text-white focus:ring-2 focus:ring-green-400 appearance-none outline-none transition">
            <option value="" class="text-gray-800">All Courses</option>
            <option class="text-gray-800">Information Technology</option>
            <option class="text-gray-800">Computer Science</option>
            <option class="text-gray-800">Business Administration</option>
            <option class="text-gray-800">Accountancy</option>
            <option class="text-gray-800">Engineering</option>
            <option class="text-gray-800">Education</option>
            <option class="text-gray-800">Nursing</option>
            <option class="text-gray-800">Criminology</option>
        </select>
        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/50 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</div>

{{-- Table --}}
<div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f3d25] text-white">
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest w-28 opacity-80">Student ID</th>
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest w-36 opacity-80">Name</th>
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest opacity-80">Email</th>
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest w-36 opacity-80">Course</th>
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest w-20 opacity-80">Year</th>
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest w-24 opacity-80">Created</th>
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest w-28 opacity-80">Status</th>
                    <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-widest w-36 opacity-80">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="student in paginated" :key="student.id">
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                        <td class="px-5 py-3.5 text-[13px] font-bold text-[#0f3d25]" x-text="student.student_id"></td>
                        <td class="px-5 py-3.5 text-[13px] font-semibold text-gray-800" x-text="student.name"></td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-500" x-text="student.email"></td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600" x-text="student.course"></td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600" x-text="student.year_level"></td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-400" x-text="student.created"></td>
                        <td class="px-5 py-3.5">
                            <span :class="student.enrolled ? 'badge-enrolled' : 'badge-not'">
                                <template x-if="student.enrolled">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                </template>
                                <template x-if="!student.enrolled">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </template>
                                <span x-text="student.status"></span>
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <template x-if="!student.enrolled">
                                    <button @click="openEnroll(student)"
                                            class="inline-flex items-center gap-1.5 bg-green-700 hover:bg-green-800 text-white text-[12px] font-bold px-3.5 py-1.5 rounded-lg transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Enroll
                                    </button>
                                </template>
                                <template x-if="student.enrolled">
                                    <button @click="deleteFingerprint(student)"
                                            class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-[12px] font-bold px-3.5 py-1.5 rounded-lg transition-colors border border-red-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove
                                    </button>
                                </template>
                                <button @click="openEdit(student)"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="filtered.length === 0">
                    <td colspan="8" class="px-5 py-14 text-center">
                        <div class="flex flex-col items-center gap-2 text-gray-400">
                            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                            <span class="text-sm font-medium">No students found</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="bg-gray-50 px-5 py-3.5 flex items-center justify-between border-t border-gray-200">
        <div class="text-sm text-gray-600">
            Showing
            <span x-text="Math.min((currentPage-1)*perPage+1, filtered.length)"></span>–<span x-text="Math.min(currentPage*perPage, filtered.length)"></span>
            of <span x-text="filtered.length"></span> students
        </div>
        <div class="flex gap-1.5">
            <button @click="currentPage > 1 && currentPage--"
                    :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-200'"
                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                ← Prev
            </button>
            <template x-for="p in pageNumbers" :key="p">
                <button @click="currentPage = p"
                        :class="currentPage === p ? 'bg-green-700 text-white border-blue-600' : 'bg-white text-gray-700 hover:bg-gray-100'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition-colors"
                        x-text="p"></button>
            </template>
            <button @click="currentPage < totalPages && currentPage++"
                    :disabled="currentPage === totalPages"
                    :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-200'"
                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                Next →
            </button>
        </div>
    </div>
</div>

{{-- Enrollment Modal --}}
<div x-show="openModal" x-cloak
     class="fixed inset-0 z-50 modal-bg"
     style="display:flex;align-items:center;justify-content:center;padding:1rem;"
     @click.self="!isScanning && (openModal = false)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg"
         style="max-height: 90vh; overflow-y: auto; margin: auto;"
         x-show="openModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        {{-- STEP 1: Student Info --}}
        <div x-show="step === 1"
             x-transition:enter="transition ease-out duration-200 delay-100"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="px-8 py-8">

            <h3 class="text-xl font-extrabold text-gray-900 mb-6">Student Information</h3>

            <div class="space-y-5">
                <div>
                    <label class="block text-[11.5px] uppercase tracking-wider font-extrabold text-gray-400 mb-[5px] pl-[5px]">Student ID <span class="text-red-500">*</span></label>
                    <input type="text" :value="selectedUser?.student_id" readonly
                           class="w-full rounded-[14px] border-gray-200 border-[1.5px] px-[18px] py-[13.5px] text-sm text-gray-900 font-semibold outline-none bg-[#fcfdff]">
                </div>
                <div class="grid grid-cols-2 gap-[14px]">
                    <div>
                        <label class="block text-[11.5px] uppercase tracking-wider font-extrabold text-gray-400 mb-[5px] pl-[5px]">First Name <span class="text-red-500">*</span></label>
                        <input type="text" :value="selectedUser?.first_name" readonly
                               class="w-full rounded-[14px] border-gray-200 border-[1.5px] px-[18px] py-[13.5px] text-sm text-gray-900 font-semibold outline-none bg-[#fcfdff]">
                    </div>
                    <div>
                        <label class="block text-[11.5px] uppercase tracking-wider font-extrabold text-gray-400 mb-[5px] pl-[5px]">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" :value="selectedUser?.last_name" readonly
                               class="w-full rounded-[14px] border-gray-200 border-[1.5px] px-[18px] py-[13.5px] text-sm text-gray-900 font-semibold outline-none bg-[#fcfdff]">
                    </div>
                </div>
                <div>
                    <label class="block text-[11.5px] uppercase tracking-wider font-extrabold text-gray-400 mb-[5px] pl-[5px]">Course/Degree <span class="text-red-500">*</span></label>
                    <input type="text" :value="selectedUser?.course" readonly
                           class="w-full rounded-[14px] border-gray-200 border-[1.5px] px-[18px] py-[13.5px] text-sm text-gray-900 font-semibold outline-none bg-[#fcfdff]">
                </div>
                <div>
                    <label class="block text-[11.5px] uppercase tracking-wider font-extrabold text-gray-400 mb-[5px] pl-[5px]">Year Level <span class="text-red-500">*</span></label>
                    <input type="text" :value="selectedUser?.year_level" readonly
                           class="w-full rounded-[14px] border-gray-200 border-[1.5px] px-[18px] py-[13.5px] text-sm text-gray-900 font-semibold outline-none bg-[#fcfdff]">
                </div>

                <div class="bg-[#eaf1ff]/50 border border-[#9dbbf7]/30 rounded-xl p-[14px] px-[18px] flex gap-[14px] items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-[#2c71fa] shrink-0 mt-[1px]">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
                    </svg>
                    <div class="pl-1 text-left w-full mt-[-1px]">
                        <p class="text-[11px] font-[800] uppercase tracking-widest text-[#1e5ee1] opacity-90 mt-[1px]">Next Step : Capture Biometrics</p>
                        <p class="text-[11px] text-[#4b669b] font-[550] leading-snug mt-1 opacity-95">Ensure all student information is accurately filled out before proceeding to the next step, which requires capturing the student's fingerprint biometrics for enrollment.</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="openModal = false"
                            class="flex-1 rounded-[14px] border-[2px] border-[#e1e5ee] bg-gray-50/50 text-[#8692a8] hover:border-[#ccd2df] hover:bg-gray-100 hover:text-gray-600 transition py-[14px] uppercase text-[12px] tracking-widest font-[800]">
                        Cancel
                    </button>
                    <button type="button" @click="step = 2"
                            class="flex-[1.5] bg-[#1e52df] hover:bg-green-700 text-white rounded-[14px] py-[14px] uppercase text-[12px] tracking-widest font-[800] shadow-lg transition">
                        Capture Biometrics !
                    </button>
                </div>
            </div>
        </div>

        {{-- STEP 2: Fingerprint Scan (original design) --}}
        <div x-show="step === 2"
             x-transition:enter="transition ease-out duration-300 delay-50"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="px-8 py-10 flex flex-col items-center">

            <div class="w-full text-center mb-5">
                <h3 class="text-[20px] font-extrabold text-gray-900 tracking-tight mb-1">Scan Your Student Fingerprint</h3>
                <p class="text-sm font-[600] tracking-wide mt-2 w-[85%] mx-auto text-[#6b7280] leading-snug">Place the same finger on the scanner 3 times to complete enrollment.</p>
            </div>

            {{-- 3-step progress dots --}}
            <div class="flex items-center gap-3 mb-6">
                <template x-for="i in [1,2,3]" :key="i">
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                                 :class="{
                                     'bg-green-500 text-white shadow-lg': captureCount >= i,
                                     'bg-green-700 text-white animate-pulse shadow-lg': captureCount === i - 1 && isScanning,
                                     'bg-gray-200 text-gray-400': captureCount < i - 1 || !isScanning && captureCount < i
                                 }">
                                <template x-if="captureCount >= i">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                                <template x-if="captureCount < i">
                                    <span x-text="i"></span>
                                </template>
                            </div>
                            <span class="text-[10px] font-bold text-gray-500" x-text="'Scan ' + i"></span>
                        </div>
                        <template x-if="i < 3">
                            <div class="w-8 h-[2px] mb-4 rounded-full transition-all duration-500"
                                 :class="captureCount >= i ? 'bg-green-400' : 'bg-gray-200'"></div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Scanner graphic with scan animation --}}
            <div class="relative w-[185px] h-[185px] flex items-center justify-center mb-5">
                <div class="absolute inset-0">
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-[3.5px] border-l-[3.5px] border-[#225fed] rounded-tl-[8px]"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-[3.5px] border-r-[3.5px] border-[#225fed] rounded-tr-[8px]"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-[3.5px] border-l-[3.5px] border-[#225fed] rounded-bl-[8px]"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-[3.5px] border-r-[3.5px] border-[#225fed] rounded-br-[8px]"></div>
                </div>
                <img src="/icons/fingerprint_scanner.png" alt="Fingerprint Sensor Scan"
                     class="w-[125px] h-[125px] object-contain transition-all duration-300"
                     :class="isScanning ? 'opacity-100 scale-105' : 'opacity-[0.85] scale-[0.98]'">
                <div x-show="isScanning" class="absolute left-8 right-8 h-[2.5px] bg-[#1cd40a] rounded-full scan-animation"></div>
            </div>

            {{-- Status message --}}
            <div class="w-full text-center min-h-[48px] mb-4">
                <p x-show="scanError" x-text="scanError"
                   class="text-sm font-bold text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-2.5 mx-4"></p>
                <p x-show="!scanError && scanMessage" x-text="scanMessage"
                   class="text-sm font-semibold text-gray-600"></p>
                <p x-show="!scanError && !scanMessage && !isScanning"
                   class="text-sm font-semibold text-gray-500">Press the button below to start scanning</p>
            </div>

            <button @click="handleScan()"
                    :disabled="isScanning"
                    :class="isScanning
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-100 shadow-none'
                        : 'bg-[#1bc602] hover:bg-green-600 shadow-[0_6px_25px_rgba(28,214,5,0.22)] active:scale-[0.96] text-white'"
                    class="w-[245px] font-[800] text-[15.5px] rounded-[18px] uppercase tracking-wider py-[15px] transition-all">
                <span x-show="!isScanning">Capture Biometrics</span>
                <span x-show="isScanning" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Scanning <span x-text="captureCount + '/3'"></span>...
                </span>
            </button>

            <button @click="step = 1; isScanning = false; captureCount = 0; captures = []; scanMessage = ''; scanError = '';"
                    x-show="!isScanning"
                    class="mt-4 text-xs text-gray-400 hover:text-gray-600 underline transition">
                ← Back to student info
            </button>
        </div>
    </div>
</div>

{{-- Floating Add Student Button --}}
<button @click="openAddModal = true; addUser = { first_name:'', last_name:'', middle_name:'', email:'', password:'', course:'Information Technology', year_level:'1st Year' }; addErrors = {};"
        class="fixed bottom-8 right-8 z-40 w-14 h-14 bg-green-700 hover:bg-green-800 text-white rounded-full shadow-2xl flex items-center justify-center transition-all hover:scale-110 active:scale-95">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
    </svg>
</button>

{{-- Edit Student Modal --}}
<div x-show="openEditModal" x-cloak
     class="fixed inset-0 z-[200]"
     style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);"
     @click.self="openEditModal = false">
    <div class="w-full h-full flex items-center justify-center p-4">
    <div x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative">

        <button @click="openEditModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <h2 class="text-xl font-extrabold text-gray-900 mb-1">Edit student</h2>
        <p class="text-xs text-gray-500 mb-6">Update account details and save changes.</p>

        <div class="space-y-4">
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Student ID <span class="text-red-500">*</span></label>
                <input type="text" :value="editUser.student_id" readonly
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 bg-gray-50 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="editUser.first_name"
                           :class="editErrors.first_name ? 'border-red-400' : 'border-gray-200'"
                           class="w-full border rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
                    <p x-show="editErrors.first_name" class="text-red-500 text-xs mt-1" x-text="editErrors.first_name"></p>
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="editUser.last_name"
                           :class="editErrors.last_name ? 'border-red-400' : 'border-gray-200'"
                           class="w-full border rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
                    <p x-show="editErrors.last_name" class="text-red-500 text-xs mt-1" x-text="editErrors.last_name"></p>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Middle Name</label>
                <input type="text" x-model="editUser.middle_name"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" x-model="editUser.email"
                       :class="editErrors.email ? 'border-red-400' : 'border-gray-200'"
                       class="w-full border rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
                <p x-show="editErrors.email" class="text-red-500 text-xs mt-1" x-text="editErrors.email"></p>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">New Password</label>
                <input type="password" x-model="editUser.password" placeholder="Leave blank to keep current"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Course/Degree <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select x-model="editUser.course" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400 appearance-none bg-white">
                        <option>Information Technology</option>
                        <option>Computer Science</option>
                        <option>Business Administration</option>
                        <option>Accountancy</option>
                        <option>Engineering</option>
                        <option>Education</option>
                        <option>Nursing</option>
                        <option>Criminology</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Year Level <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select x-model="editUser.year_level" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400 appearance-none bg-white">
                        <option>1st Year</option>
                        <option>2nd Year</option>
                        <option>3rd Year</option>
                        <option>4th Year</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button @click="openEditModal = false"
                    class="flex-1 py-3 border-2 border-gray-200 text-gray-500 rounded-xl font-bold text-sm hover:bg-gray-50 transition uppercase tracking-wider">
                Cancel
            </button>
            <button @click="saveEdit()"
                    :disabled="isSaving"
                    class="flex-1 py-3 bg-green-700 hover:bg-green-800 text-white rounded-xl font-bold text-sm transition uppercase tracking-wider disabled:opacity-60">
                <span x-show="!isSaving">Save Changes</span>
                <span x-show="isSaving">Saving...</span>
            </button>
        </div>
    </div>
    </div>{{-- /flex wrapper --}}
</div>

{{-- Add Student Modal --}}
<div x-show="openAddModal" x-cloak
     class="fixed inset-0 z-[200]"
     style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);"
     @click.self="openAddModal = false">
    <div class="w-full h-full flex items-center justify-center p-4">
    <div x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative">

        <button @click="openAddModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <h2 class="text-xl font-extrabold text-gray-900 mb-1">Add Student</h2>
        <p class="text-xs text-gray-500 mb-6">Create a new student account.</p>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="addUser.first_name"
                           :class="addErrors.first_name ? 'border-red-400' : 'border-gray-200'"
                           class="w-full border rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
                    <p x-show="addErrors.first_name" class="text-red-500 text-xs mt-1" x-text="addErrors.first_name"></p>
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="addUser.last_name"
                           :class="addErrors.last_name ? 'border-red-400' : 'border-gray-200'"
                           class="w-full border rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
                    <p x-show="addErrors.last_name" class="text-red-500 text-xs mt-1" x-text="addErrors.last_name"></p>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Middle Name</label>
                <input type="text" x-model="addUser.middle_name"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" x-model="addUser.email"
                       :class="addErrors.email ? 'border-red-400' : 'border-gray-200'"
                       class="w-full border rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
                <p x-show="addErrors.email" class="text-red-500 text-xs mt-1" x-text="addErrors.email"></p>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" x-model="addUser.password"
                       :class="addErrors.password ? 'border-red-400' : 'border-gray-200'"
                       class="w-full border rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400">
                <p x-show="addErrors.password" class="text-red-500 text-xs mt-1" x-text="addErrors.password"></p>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Course/Degree <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select x-model="addUser.course" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400 appearance-none bg-white">
                        <option>Information Technology</option>
                        <option>Computer Science</option>
                        <option>Business Administration</option>
                        <option>Accountancy</option>
                        <option>Engineering</option>
                        <option>Education</option>
                        <option>Nursing</option>
                        <option>Criminology</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 mb-1">Year Level <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select x-model="addUser.year_level" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-400 appearance-none bg-white">
                        <option>1st Year</option>
                        <option>2nd Year</option>
                        <option>3rd Year</option>
                        <option>4th Year</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button @click="openAddModal = false"
                    class="flex-1 py-3 border-2 border-gray-200 text-gray-500 rounded-xl font-bold text-sm hover:bg-gray-50 transition uppercase tracking-wider">
                Cancel
            </button>
            <button @click="saveAdd()"
                    :disabled="isSaving"
                    class="flex-1 py-3 bg-green-700 hover:bg-green-800 text-white rounded-xl font-bold text-sm transition uppercase tracking-wider disabled:opacity-60">
                <span x-show="!isSaving">Add Student</span>
                <span x-show="isSaving">Adding...</span>
            </button>
        </div>
    </div>
    </div>{{-- /flex wrapper --}}
</div>

@endsection

