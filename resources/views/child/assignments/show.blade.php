@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('child.assignments') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                &larr; Back to Assignments
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $assignment->title }}</h1>
        </div>

        <!-- Assignment Details -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Due Date</p>
                    <p class="font-medium {{ $assignment->is_overdue ? 'text-red-600' : '' }}">
                        {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="px-2 py-1 text-xs rounded-full capitalize
                        @if($assignment->status === 'completed') bg-green-100 text-green-800
                        @elseif($assignment->status === 'submitted_for_review') bg-purple-100 text-purple-800
                        @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucwords(str_replace('_', ' ', $assignment->status)) }}
                    </span>
                </div>
            </div>

            <div class="border-t pt-4">
                <h3 class="font-semibold text-gray-900 mb-2">What to do</h3>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $assignment->description }}</p>
            </div>

            @if($assignment->feedback)
                <div class="border-t mt-4 pt-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Feedback from Parent</h3>
                    <p class="text-gray-700">{{ $assignment->feedback }}</p>
                    @if($assignment->rating)
                        <div class="mt-2 flex items-center">
                            <span class="text-sm text-gray-500 mr-2">Rating:</span>
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $assignment->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Submission Form -->
        @if($assignment->status === 'completed')
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                <div class="text-4xl mb-2">🎉</div>
                <h3 class="text-lg font-semibold text-green-800">Great job!</h3>
                <p class="text-green-700 mt-1">This assignment is complete.</p>
            </div>
        @elseif($assignment->status === 'submitted_for_review')
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 text-center">
                <div class="text-4xl mb-2">⏳</div>
                <h3 class="text-lg font-semibold text-purple-800">Waiting for review</h3>
                <p class="text-purple-700 mt-1">You've already submitted this. Your parent/tutor will review it soon.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">📤 Your Submission</h3>
                <form action="{{ route('child.assignments.submit', $assignment) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            What did you learn or do?
                        </label>
                        <textarea name="submission_text" rows="6"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500"
                                  placeholder="Write your answer, reflection, or describe what you did..."
                                  required>{{ old('submission_text') }}</textarea>
                        @error('submission_text')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Attach a photo or file (optional)
                        </label>
                        <input type="file" name="attachment"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('attachment')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Assignment
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection