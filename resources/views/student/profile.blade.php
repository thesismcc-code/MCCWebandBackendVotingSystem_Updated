@extends('components.student-layout')
@section('title', 'My Profile — MCC Student Portal')

@section('content')

<div class="flex items-center gap-3 mb-6">
    <button onclick="history.back()"
        class="w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-105"
        style="background:white;border:1px solid #e8ede3;color:#1a3a1a;box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </button>
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-0.02em;">My Profile</h1>
        <p class="text-gray-400 text-sm mt-0.5">Manage your account information</p>
    </div>
</div>

<div class="s-card p-8 relative overflow-hidden">
    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#1a5c38,#2d7a52,#4ade80);"></div>

    {{-- Avatar row --}}
    <div class="flex flex-wrap items-center justify-between gap-5 mb-8 pb-8" style="border-bottom:1px solid #f0f4eb;">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-white text-3xl font-extrabold flex-shrink-0"
                 style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 6px 20px rgba(26,92,56,.3);">
                {{ strtoupper(substr(session('auth_user.first_name', 'S'), 0, 1)) }}
            </div>
            <div>
                <div class="text-lg font-extrabold text-gray-900">
                    {{ session('auth_user.first_name', '') }} {{ session('auth_user.last_name', '') }}
                </div>
                <div class="text-sm text-gray-400 mt-0.5">{{ session('auth_user.email', '') }}</div>
                <span class="inline-flex items-center gap-1.5 mt-2 text-[11px] font-bold px-2.5 py-1 rounded-full" style="background:#e8f5ee;color:#1a5c38;">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                    Student
                </span>
            </div>
        </div>
        <button type="submit" form="profile-form"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-bold text-sm transition-all hover:-translate-y-0.5 hover:shadow-lg"
            style="background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 4px 12px rgba(26,92,56,.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Update Profile
        </button>
    </div>

    {{-- Form --}}
    <form id="profile-form" action="{{ route('update-profile') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">First Name</label>
                <input type="text" name="first_name"
                    value="{{ session('auth_user.first_name', '') }}"
                    class="w-full px-4 py-3 rounded-xl border text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 transition-all"
                    style="border-color:#e5e7eb;background:#fafafa;focus:ring-color:#1a5c38;">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Last Name</label>
                <input type="text" name="last_name"
                    value="{{ session('auth_user.last_name', '') }}"
                    class="w-full px-4 py-3 rounded-xl border text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 transition-all"
                    style="border-color:#e5e7eb;background:#fafafa;">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Email Address</label>
                <input type="email" name="email"
                    value="{{ session('auth_user.email', '') }}"
                    placeholder="example@gmail.com"
                    class="w-full px-4 py-3 rounded-xl border text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 transition-all"
                    style="border-color:#e5e7eb;background:#fafafa;">
                @if(!session('auth_user.email'))
                <div class="flex items-center gap-2 mt-2">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0" style="background:#dc2626;">!</span>
                    <span class="text-xs text-gray-500">Enter your email to activate your account</span>
                </div>
                @endif
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Degree / Course</label>
                <input type="text" name="course"
                    value="{{ session('auth_user.course', '') }}"
                    class="w-full px-4 py-3 rounded-xl border text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 transition-all"
                    style="border-color:#e5e7eb;background:#fafafa;">
            </div>
        </div>
    </form>
</div>
@endsection
