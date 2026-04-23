@extends('components.admin-layout')

@section('title', 'Election History')
@section('page-title', 'Election History')
@section('page-sub', 'View all past elections and their results')

@section('content')
<div>
    @if(empty($elections))
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
            <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-lg font-semibold">No election history yet</p>
            <p class="text-sm mt-1">Past elections will appear here once they are closed.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($elections as $id => $election)
            @php
                $status = $election['status'] ?? 'closed';
                $name = $election['election_name'] ?? 'Unnamed Election';
                $semester = $election['semester'] ?? '';
                $academicYear = $election['academic_year'] ?? '';
                $dateFrom = $election['date_from'] ?? null;
                $dateTo = $election['date_to'] ?? null;
                $openingTime = $election['opening_time'] ?? null;
                $closingTime = $election['closing_time'] ?? null;
                $createdAt = $election['created_at'] ?? null;
            @endphp
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-base font-bold text-gray-900">{{ $name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                            @if($status === 'active') bg-green-100 text-green-700
                            @elseif($status === 'upcoming') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-gray-500">
                        @if($semester)
                            <span><span class="font-medium text-gray-700">Semester:</span> {{ $semester }}</span>
                        @endif
                        @if($academicYear)
                            <span><span class="font-medium text-gray-700">A.Y.:</span> {{ $academicYear }}</span>
                        @endif
                        @if($dateFrom && $dateTo)
                            <span><span class="font-medium text-gray-700">Date:</span>
                                {{ \Carbon\Carbon::parse($dateFrom)->format('M d') }} – {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                            </span>
                        @endif
                        @if($openingTime && $closingTime)
                            <span><span class="font-medium text-gray-700">Time:</span>
                                {{ \Carbon\Carbon::parse($openingTime)->format('g:i A') }} – {{ \Carbon\Carbon::parse($closingTime)->format('g:i A') }}
                            </span>
                        @endif
                        @if($createdAt)
                            <span><span class="font-medium text-gray-700">Created:</span>
                                {{ \Carbon\Carbon::parse($createdAt)->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="shrink-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#1a5c38,#2d7a52);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
