@extends('layouts.app')

@section('title', 'Assignments')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Assignments</h1>
                <p class="text-gray-600 mt-1">Manage tasks for your children.</p>
            </div>
            <a href="{{ route('parent.assignments.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> New Assignment
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($assignments->isEmpty())
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📝</div>
                    <h3 class="text-lg font-medium text-gray-900">No assignments yet</h3>
                    <p class="text-gray-500 mt-1">Create your first assignment for your child.</p>
                    <a href="{{ route('parent.assignments.create') }}"
                       class="mt-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Create Assignment
                    </a>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Child</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $assignment->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $assignment->child->first_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                                    @if($assignment->is_overdue)
                                        <span class="ml-1 text-red-600 text-xs">(Overdue)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full capitalize
                                        @if($assignment->priority === 'high') bg-red-100 text-red-800
                                        @elseif($assignment->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $assignment->priority ?? 'medium' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full capitalize
                                        @if($assignment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($assignment->status === 'submitted_for_review') bg-purple-100 text-purple-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ str_replace('_', ' ', $assignment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('parent.assignments.show', $assignment) }}"
                                       class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection