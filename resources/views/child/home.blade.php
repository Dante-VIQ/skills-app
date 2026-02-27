@extends('layouts.app')

@section('title', 'My Learning Dashboard')

@section('content')
<div class="py-16">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Hello, {{ Auth::user()->first_name }}! 👋
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Ready to learn something amazing today?
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Impact Badge -->
                    <div class="bg-gradient-to-r from-green-400 to-blue-500 rounded-lg px-4 py-2 text-white">
                        <div class="text-sm">My Impact</div>
                        <div class="text-xl font-bold">${{ number_format($stats['impact_contributed'] ?? 0, 2) }}</div>
                    </div>

                    <!-- Streak Badge -->
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg px-4 py-2 text-white">
                        <div class="text-sm">Day Streak</div>
                        <div class="text-xl font-bold">{{ $stats['streak_days'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="skilldna-card bg-white text-center p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['completed_modules'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Lessons Completed</div>
            </div>
            <div class="skilldna-card bg-white text-center p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-green-600">{{ $stats['total_watch_time'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Minutes Watched</div>
            </div>
            <div class="skilldna-card bg-white text-center p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-purple-600">{{ count($pendingAssignments) }}</div>
                <div class="text-sm text-gray-600">Pending Tasks</div>
            </div>
            <div class="skilldna-card bg-white text-center p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['average_score'] ?? 0 }}%</div>
                <div class="text-sm text-gray-600">Average Score</div>
            </div>
        </div>

        <!-- Refresh Recommendations Button -->
        {{-- <div class="flex justify-end mb-4">
            <a href="{{ route('child.refresh-recommendations') }}"
               class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-sync-alt mr-1"></i> Refresh Recommendations
            </a>
        </div> --}}

        <!-- Pending Assignments -->
        @if($pendingAssignments->count() > 0)
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">My Tasks</h2>
                <a href="{{ route('child.assignments') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($pendingAssignments as $assignment)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $assignment->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($assignment->description, 60) }}</p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <i class="far fa-clock mr-1"></i>
                                Due {{ $assignment->due_date->diffForHumans() }}
                            </div>
                        </div>
                        <a href="{{ route('child.assignment.show', $assignment) }}"
                           class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                            Start
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recommended for You -->
        @if($recommendedContent->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recommended for You</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recommendedContent as $content)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="h-32 bg-gradient-to-br from-purple-400 to-pink-500 relative">
                        @if($content->thumbnail)
                            <img src="{{ Storage::url($content->thumbnail) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-play-circle text-white text-4xl"></i>
                            </div>
                        @endif
                        <span class="absolute top-2 right-2 bg-white/90 text-xs px-2 py-1 rounded">
                            {{ $content->duration ?? 10 }} min
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center mb-2">
                            <span class="text-xs font-semibold text-{{ $content->category->color ?? 'blue' }}-600 bg-{{ $content->category->color ?? 'blue' }}-100 px-2 py-1 rounded">
                                {{ $content->category->name ?? 'General' }}
                            </span>
                            <span class="ml-auto text-xs text-gray-500">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                {{ $content->difficulty ?? 'Beginner' }}
                            </span>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">{{ $content->title }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($content->description, 60) }}</p>
                        <a href="{{ route('child.learn.show', $content) }}"
                           class="block w-full text-center py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-md hover:opacity-90">
                            Watch Now
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Trending Content -->
        @if($trendingContent->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🔥 Trending Now</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($trendingContent as $content)
                <div class="bg-white p-4 rounded-lg shadow flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-fire text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $content->title }}</h4>
                        <p class="text-xs text-gray-500">{{ $content->weekly_views ?? 0 }} views this week</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Skill Gap Content -->
        @if($skillGapContent->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">📚 Improve Your Skills</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($skillGapContent as $content)
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h4 class="font-medium text-gray-900">{{ $content->title }}</h4>
                    <p class="text-xs text-gray-600 mt-1">{{ $content->category->name }}</p>
                    <a href="{{ route('child.learn.show', $content) }}" class="mt-2 text-xs text-yellow-600 hover:text-yellow-800">
                        Start learning →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Next in Series -->
        @if($nextInSeries->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">⏭️ Continue Learning</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($nextInSeries as $content)
                <div class="bg-blue-50 p-4 rounded-lg flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-play text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $content->title }}</h4>
                        <p class="text-xs text-gray-600">Next in series</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Activity</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if($recentActivities->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Impact</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentActivities as $activity)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activity->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->contentModule->category->name ?? 'General' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                +${{ number_format($activity->calculateImpact() ?? 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-8">
                    <p class="text-gray-500">No activity yet. Start learning to see your progress!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
