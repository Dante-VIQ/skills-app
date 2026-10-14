@extends('layouts.app')

@section('title', 'Create Assignment')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('parent.assignments') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                &larr; Back to Assignments
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Create Assignment</h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('parent.assignments.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Child</label>
                    <select name="child_id" class="w-full rounded-lg border-gray-300" required>
                        <option value="">-- Choose Child --</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>
                                {{ $child->first_name }} {{ $child->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('child_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full rounded-lg border-gray-300" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="5" class="w-full rounded-lg border-gray-300" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="w-full rounded-lg border-gray-300">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                               class="w-full rounded-lg border-gray-300" required>
                        @error('due_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('parent.assignments') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection