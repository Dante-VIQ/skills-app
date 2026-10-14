@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Assignments 📝</h1>
            <p class="text-gray-600 mt-1">Complete these tasks to earn points and unlock badges.</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                <p class="text-sm text-yellow-700">Pending</p>
                <p class="text-2xl font-bold text-yellow-900">{{ $pending }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                <p class="text-sm text-green-700">Completed</p>
                <p class="text-2xl font-bold text-green-900">{{ $completed }}</p>
            </div>
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 col-span-2 md:col-span-1">
                <p class="text-sm text-indigo-700">Total</p>
                <p class="text-2xl font-bold text-indigo-900">{{ $assignments->total() }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Assignments List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($assignments->isEmpty())
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🎉</div>
                    <h3 class="text-lg font-medium text-gray-900">No assignments yet</h3>
                    <p class="text-gray-500 mt-1">When your parent assigns a task, it will show up here.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($assignments as $assignment)
                        <a href="{{ route('child.assignments.show', $assignment) }}"
                           class="block p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $assignment->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-1">
                                        {{ Str::limit($assignment->description, 100) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Due {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                                        @if($assignment->is_overdue)
                                            <span class="text-red-600 ml-1">(Overdue!)</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="ml-4">
                                    @php
                                        $statusStyles = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'submitted_for_review' => 'bg-purple-100 text-purple-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                        ];
                                        $style = $statusStyles[$assignment->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $style }}">
                                        {{ ucwords(str_replace('_', ' ', $assignment->status)) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="px-4 py-3 border-t">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection