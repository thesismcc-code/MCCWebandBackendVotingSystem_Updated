@extends('components.admin-layout')
@section('title', 'Quick Access — MCC Voting System')
@section('page-title', 'Quick Access')
@section('page-sub', 'Navigate to key system modules')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

    @include('components.quick-access-card', [
        'title' => 'Manage Accounts',
        'desc'  => 'Create and manage user roles',
        'icon_bg' => 'bg-green-700',
        'route' => route('view.manage-accounts'),
        'icon_path' => '/icons/person.png'
    ])

    @include('components.quick-access-card', [
        'title' => 'Fingerprint Enrollment',
        'desc'  => 'Register student biometric data',
        'icon_bg' => 'bg-emerald-500',
        'route' => route('view.finger-print'),
        'icon_path' => '/icons/fingerprint.png'
    ])

    @include('components.quick-access-card', [
        'title' => 'Election Control',
        'desc'  => 'Configure election settings',
        'icon_bg' => 'bg-amber-500',
        'route' => route('view.election-control'),
        'icon_path' => '/icons/settings.png'
    ])

    @include('components.quick-access-card', [
        'title' => 'Student Eligibility',
        'desc'  => 'Track email verification status',
        'icon_bg' => 'bg-teal-600',
        'route' => route('view.student-eligibility'),
        'icon_path' => '/icons/beenhere.png'
    ])

    @include('components.quick-access-card', [
        'title' => 'Voting Logs',
        'desc'  => 'View all voting records',
        'icon_bg' => 'bg-green-600',
        'route' => route('view.voting-logs'),
        'icon_path' => '/icons/how_to_vote.png',
    ])

    @include('components.quick-access-card', [
        'title' => 'Security Logs',
        'desc'  => 'Monitor security events',
        'icon_bg' => 'bg-rose-500',
        'route' => route('view.security-logs'),
        'icon_path' => '/icons/earthquake.png'
    ])

    @include('components.quick-access-card', [
        'title' => 'System Activity',
        'desc'  => 'Monitor real-time system logs',
        'icon_bg' => 'bg-green-700',
        'route' => route('view.system-activity'),
        'icon_path' => '/icons/earthquake.png'
    ])

    @include('components.quick-access-card', [
        'title' => 'Reports & Analytics',
        'desc'  => 'Generate and export reports',
        'icon_bg' => 'bg-emerald-700',
        'route' => route('view.reports-and-analytics'),
        'icon_path' => '/icons/chart_data.png'
    ])

</div>

@endsection
