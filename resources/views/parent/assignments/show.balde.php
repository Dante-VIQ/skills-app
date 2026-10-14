@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('parent.assignments') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                &larr; Back to Assignments
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $assignment->title }}</h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Child</p>
                    <p class="font-medium">
                        {{ $assignment->child->first_name ?? 'N/A' }} {{ $assignment->child->last_name ?? '' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Due Date</p>
                    <p class="font-medium {{ $assignment->is_overdue ? 'text-red-600' : '' }}">
                        {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Priority</p>
                    <span class="px-2 py-1 text-xs rounded-full capitalize
                        @if($assignment->priority === 'high') bg-red-100 text-red-800
                        @elseif($assignment->priority === 'medium') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $assignment->priority ?? 'medium' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="px-2 py-1 text-xs rounded-full capitalize
                        @if($assignment->status === 'completed') bg-green-100 text-green-800
                        @elseif($assignment->status === 'submitted_for_review') bg-purple-100 text-purple-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ str_replace('_', ' ', $assignment->status) }}
                    </span>
                </div>
            </div>

            <div class="border-t pt-4">
                <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $assignment->description }}</p>
            </div>

            @if($assignment->feedback)
                <div class="border-t mt-4 pt-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Feedback</h3>
                    <p class="text-gray-700">{{ $assignment->feedback }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection