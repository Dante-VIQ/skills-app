@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Welcome back, {{ Auth::user()->first_name }}!
                </h1>
                <p class="mt-2 text-gray-600">
                    Here's what's happening with your children's learning journey.
                </p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="skilldna-card bg-gradient-to-br from-blue-50 to-blue-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Children</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $children->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="skilldna-card bg-gradient-to-br from-green-50 to-green-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Completed This Week</p>
                            {{-- <p class="text-2xl font-semibold text-gray-900">{{ $recentActivities->count() }}</p> --}}
                        </div>
                    </div>
                </div>

                <div class="skilldna-card bg-gradient-to-br from-purple-50 to-purple-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-hand-holding-heart text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Family Impact</p>
                            {{-- <p class="text-2xl font-semibold text-gray-900">${{ number_format($totalImpact, 2) }}</p> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Children Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">My Children</h2>
                    @permission('manage children')
                        <a href="{{ route('parent.add-child') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-2"></i> Add Child
                        </a>
                    @endpermission
                </div>

                @if ($children->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($children as $child)
                            <div class="skilldna-card bg-white hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        @if ($child->avatar)
                                            <img src="{{ Storage::url($child->avatar) }}" alt="{{ $child->full_name }}"
                                                class="w-16 h-16 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                                {{ $child->initials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $child->full_name }}
                                            @if ($child->age)
                                                <span class="text-sm text-gray-500">({{ $child->age }} years)</span>
                                            @endif
                                        </h3>
                                        <div class="mt-2 space-y-1">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-tasks mr-2"></i>
                                                <span>{{ $child->pending_assignments_count ?? 0 }} pending
                                                    assignments</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span>{{ $child->total_watch_time ?? 0 }} min watched</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-star mr-2"></i>
                                                <span>{{ $child->skills ? count($child->skills) : 0 }} skills</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between">
                                        <a href="{{ route('parent.activities', $child) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Activities
                                        </a>
                                        @if ($child->pending_assignments_count > 0)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $child->pending_assignments_count }} pending
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('parent.children.resend-credentials', $child) }}"
                                    class="block">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-envelope mr-2"></i>Resend Credentials
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-child text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No children yet</h3>
                        <p class="text-gray-600 mb-4">Add your first child to start their learning journey.</p>
                        @permission('manage children')
                            <a href="{{ route('parent.add-child') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Add First Child
                            </a>
                        @endpermission
                    </div>
                @endif
            </div>

            <!-- Recent Activity & Upcoming Assignments -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Activities -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Activities</h2>
                    <div class="skilldna-card bg-white">
                        {{-- @if ($recentActivities->count() > 0)
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @foreach ($recentActivities as $activity)
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                                                    <i class="fas fa-book-open text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $activity->child->full_name }}
                                                </p>
                                                <p class="text-sm text-gray-500 truncate">
                                                    {{ $activity->title }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-900">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </p>
                                                @if ($activity->score)
                                                    <p class="text-xs text-gray-500">
                                                        Score: {{ $activity->score }}/{{ $activity->max_score }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No recent activities yet.</p>
                        </div>
                    @endif --}}
                    </div>
                </div>

                <!-- Upcoming Assignments -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Assignments</h2>
                    <div class="skilldna-card bg-white">
                        {{-- @if ($upcomingAssignments->count() > 0)
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @foreach ($upcomingAssignments as $assignment)
                                    <li class="py-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $assignment->title }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    For: {{ $assignment->child->full_name }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm {{ $assignment->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                                                    Due {{ $assignment->due_date->diffForHumans() }}
                                                </p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $assignment->is_overdue ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $assignment->is_overdue ? 'Overdue' : 'Pending' }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No upcoming assignments.</p>
                        </div>
                    @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
