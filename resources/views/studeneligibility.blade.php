@extends('components.admin-layout')
@section('title', 'Student Eligibility — MCC Voting System')
@section('page-title', 'Student Eligibility')
@section('page-sub', 'Verify if a student is allowed to vote')

@section('content')

<div x-data="eligibilityApp()">

<input type="hidden" id="currentStatus" value="{{ request('status') }}">
<input type="hidden" id="currentSearch" value="{{ $search }}">
<input type="hidden" id="currentCourse" value="{{ $course }}">

{{-- Stat Cards --}}
@php $statusFilter = request('status'); @endphp
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div onclick="filterByStatus('')"
         class="bg-white border rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4 cursor-pointer hover:-translate-y-0.5 transition-all {{ !$statusFilter ? 'border-[#1a5c38] ring-1 ring-[#1a5c38]' : 'border-gray-100' }}">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0ZM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
        </div>
        <div><p class="text-3xl font-extrabold text-gray-900">{{ $total }}</p><p class="text-xs font-semibold text-gray-500 mt-0.5">Total Students</p></div>
    </div>
    <div onclick="filterByStatus('eligible')"
         class="bg-white border rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4 cursor-pointer hover:-translate-y-0.5 transition-all {{ $statusFilter === 'eligible' ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-100' }}">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-green-100">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <div><p class="text-3xl font-extrabold text-gray-900">{{ $eligible }}</p><p class="text-xs font-semibold text-gray-500 mt-0.5">Eligible</p></div>
    </div>
    <div onclick="filterByStatus('not_eligible')"
         class="bg-white border rounded-2xl shadow-sm px-5 py-4 flex items-center gap-4 cursor-pointer hover:-translate-y-0.5 transition-all {{ $statusFilter === 'not_eligible' ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-100' }}">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-red-100">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </div>
        <div><p class="text-3xl font-extrabold text-gray-900">{{ $notEligible }}</p><p class="text-xs font-semibold text-gray-500 mt-0.5">Not Eligible</p></div>
    </div>
</div>

{{-- Filters --}}
<form id="filterForm" method="GET" action="{{ route('view.student-eligibility') }}" class="flex flex-col sm:flex-row gap-3 mb-5">
    <input type="hidden" name="status" id="statusInput" value="{{ request('status') }}">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Search by Student ID or Name" autocomplete="off"
               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-700 placeholder-gray-400">
    </div>
    <div class="relative sm:w-64 shrink-0" x-data="{ openFilter: false }">
        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false"
                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
            <span>{{ $course && $course !== 'all' ? $course : 'All Courses' }}</span>
            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openFilter }" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <input type="hidden" name="course" id="courseInput" x-ref="courseInput" value="{{ $course }}">
        <ul x-show="openFilter" x-transition x-cloak class="absolute left-0 mt-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-lg py-1.5 z-30 text-sm">
            @php $courses = ['all' => 'All Courses', 'Computer Science' => 'Computer Science', 'Information Technology' => 'Information Technology', 'Business Administration' => 'Business Administration', 'Education' => 'Education', 'Nursing' => 'Nursing', 'Criminology' => 'Criminology', 'Accountancy' => 'Accountancy', 'Engineering' => 'Engineering']; @endphp
            @foreach($courses as $val => $label)
            <li class="px-4 py-2 font-medium cursor-pointer hover:bg-green-50 hover:text-[#1a5c38] transition-colors {{ ($course === $val || (!$course && $val === 'all')) ? 'text-[#1a5c38] font-bold' : 'text-gray-700' }}"
                @click="$refs.courseInput.value = '{{ $val }}'; openFilter = false; document.getElementById('filterForm').submit()">{{ $label }}</li>
            @endforeach
        </ul>
    </div>
    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all shrink-0" style="background:linear-gradient(135deg,#2d7a52,#1a5c38);">Search</button>
</form>

{{-- Table --}}
<div class="rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#1a5c38;">
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Student ID</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Name</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Course</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Email</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-white uppercase tracking-widest">Status</th>
                    <th class="px-5 py-3.5 text-center text-[11px] font-bold text-white uppercase tracking-widest">View</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($students as $student)
                @php $isEligible = !empty($student->getEmailVerifiedAt()); $fullName = trim($student->getFirstName() . ' ' . $student->getLastName()); @endphp
                <tr class="hover:bg-green-50/30 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-[13px]" style="color:#1a5c38;">{{ $student->getStudentId() ?? '—' }}</td>
                    <td class="px-5 py-3.5 font-semibold text-gray-800 text-[13px]">{{ $fullName }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $student->getCourse() ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-[13px]">{{ $isEligible ? 'Verified' : 'Not Verified' }}</td>
                    <td class="px-5 py-3.5">
                        @if($isEligible)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Eligible</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Not Eligible</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <button @click="openModal = true; selectedStudent = { name: '{{ addslashes($fullName) }}', studentId: '{{ $student->getStudentId() }}', email: '{{ $student->getEmail() }}', course: '{{ addslashes($student->getCourse() ?? '—') }}', yearLevel: '{{ $student->getYearLevel() ?? '—' }}', eligible: {{ $isEligible ? 'true' : 'false' }}, verifiedAt: '{{ $student->getEmailVerifiedAt() ?? '' }}' }"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z"/></svg>
                            View
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-14 text-center"><p class="text-sm font-medium text-gray-300">No students found.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($students->hasPages())
<div class="flex justify-center mt-5">{{ $students->links() }}</div>
@endif

{{-- Detail Modal --}}
<div x-cloak x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm" @click.self="openModal = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Student Details</h2>
            <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="px-6 py-5 space-y-3 text-sm">
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400 font-medium">Student ID</span><span class="font-bold text-gray-800" x-text="selectedStudent.studentId"></span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400 font-medium">Full Name</span><span class="font-bold text-gray-800" x-text="selectedStudent.name"></span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400 font-medium">Email</span><span class="font-semibold text-gray-700" x-text="selectedStudent.email"></span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400 font-medium">Course</span><span class="font-semibold text-gray-700" x-text="selectedStudent.course"></span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400 font-medium">Year Level</span><span class="font-semibold text-gray-700" x-text="selectedStudent.yearLevel"></span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400 font-medium">Email Status</span><span class="font-bold" :class="selectedStudent.eligible ? 'text-green-600' : 'text-red-500'" x-text="selectedStudent.eligible ? 'Verified' : 'Not Verified'"></span></div>
            <div class="flex justify-between py-2"><span class="text-gray-400 font-medium">Voting Eligibility</span>
                <span class="font-bold flex items-center gap-1.5" :class="selectedStudent.eligible ? 'text-green-600' : 'text-red-500'">
                    <template x-if="selectedStudent.eligible"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg></template>
                    <template x-if="!selectedStudent.eligible"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></template>
                    <span x-text="selectedStudent.eligible ? 'Eligible' : 'Not Eligible'"></span>
                </span>
            </div>
        </div>
    </div>
</div>

</div>

<script>
    function eligibilityApp() { return { openModal: false, selectedStudent: {} } }
    let searchTimer = null;
    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 400);
    });
    function filterByStatus(status) {
        document.getElementById('statusInput').value = status;
        document.getElementById('filterForm').submit();
    }
</script>

@endsection
