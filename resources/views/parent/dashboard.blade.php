<x-app-layout>

@section('title', 'Parent Dashboard')


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->getDisplayName() }}! 👋</h1>
                <p class="text-blue-100">Track your children's learning and impact</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('parent.add-child') }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Child
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="skilldna-card bg-white p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 3.75l-5.5 5.5-2.75-2.75"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    {{-- <h3 class="text-lg font-semibold text-gray-900">{{ $children->count() > 0 }}</h3> --}}
                    <p class="text-sm text-gray-500">Children Learning</p>
                </div>
            </div>
        </div>

        <div class="skilldna-card bg-white p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">0</h3>
                    <p class="text-sm text-gray-500">Assignments Completed</p>
                </div>
            </div>
        </div>

        <div class="skilldna-card bg-white p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">₦0</h3>
                    <p class="text-sm text-gray-500">Impact Generated</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Children Grid -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Your Children</h2>
            <a href="{{ route('parent.add-child') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                + Add New
            </a>
        </div>

@if($children->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($children as $child)
            <div class="skilldna-card bg-white p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if($child->avatar)
                            <img src="{{ Storage::url($child->avatar) }}" 
                                 alt="{{ $child->first_name }}" 
                                 class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                {{ substr($child->first_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $child->first_name }} {{ $child->last_name }}</h3>
                        <p class="text-sm text-gray-500">
                            Age: {{ $child->age ?? 'N/A' }} • 
                            {{ $child->onboarding_completed ? 'Active' : 'Needs Onboarding' }}
                        </p>
                        
                        @if($child->username)
                            <p class="text-xs text-gray-400 mt-1">
                                Username: {{ $child->username }}
                            </p>
                        @endif
                        
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <a href="{{ route('parent.activities', $child) }}" 
                               class="text-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition">
                                View Progress
                            </a>
                            <a href="#" 
                               class="text-center px-3 py-2 bg-purple-50 text-purple-600 rounded-lg text-sm font-medium hover:bg-purple-100 transition">
                                Assign Task
                            </a>
                        </div>
                        
                        {{-- <div class="mt-3 flex space-x-2">
                            <a href="{{ route('parent.children.edit', $child) }}" 
                               class="text-xs text-blue-600 hover:text-blue-800">
                                Edit
                            </a>
                            <form action="{{ route('parent.children.destroy', $child) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to remove this child?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </form>
                        </div> --}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-12 bg-gray-50 rounded-2xl">
        <div class="w-24 h-24 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 3.75l-5.5 5.5-2.75-2.75"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No children added yet</h3>
        <p class="text-gray-500 mb-4">Add your first child to start their learning journey</p>
        <a href="{{ route('parent.add-child') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg font-semibold hover:shadow-md transition">
            Add Your First Child
        </a>
    </div>
@endif

    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="skilldna-card bg-white p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('parent.assignments') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Create Assignment</div>
                        <div class="text-sm text-gray-500">Set learning tasks for your children</div>
                    </div>
                </a>
                <a href="#" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Set Watch Time Limits</div>
                        <div class="text-sm text-gray-500">Manage screen time and learning goals</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="skilldna-card bg-white p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Impact</h3>
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-teal-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                        🌱
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">No impact yet</div>
                        <div class="text-sm text-gray-500">Start learning to generate impact</div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <a href="{{ route('impact.portal') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                        View all impact projects →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>