@extends('layouts.app')

@section('title', 'Add Child - SkillDNA')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Add a Child</h1>
            <p class="text-gray-600 mt-1">Create a learning account for your child</p>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('parent.children.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text"
                                   name="first_name"
                                   id="first_name"
                                   value="{{ old('first_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text"
                                   name="last_name"
                                   id="last_name"
                                   value="{{ old('last_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text"
                                   name="username"
                                   id="username"
                                   value="{{ old('username') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">Child will use this to log in</p>
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date"
                                   name="date_of_birth"
                                   id="date_of_birth"
                                   value="{{ old('date_of_birth') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-medium text-blue-800 mb-2">What happens next?</h3>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• A temporary password will be generated for your child</li>
                            <li>• Your child can log in with their username and temporary password</li>
                            <li>• They'll be guided through a fun onboarding process</li>
                            <li>• You'll be able to create assignments and track their progress</li>
                        </ul>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('parent.dashboard') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Add Child
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
