@extends('layouts.app')

@section('title', 'My Progress')

@section('content')
<div class="py-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Progress 📊</h1>
            <p class="text-gray-600 mt-1">Track how far you've come on your learning journey.</p>
        </div>

        <!-- Top Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Lessons This Week</p>
                        <p class="text-3xl font-bold text-indigo-600">
                            {{ $weeklyProgress['lessons_completed'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Assignments Done</p>
                        <p class="text-3xl font-bold text-green-600">
                            {{ $weeklyProgress['assignments_completed'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Impact Generated</p>
                        <p class="text-3xl font-bold text-pink-600">
                            ${{ number_format($weeklyProgress['impact_generated'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-pink-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Learning Streak</p>
                        <p class="text-3xl font-bold text-orange-600">
                            {{ $streak }}🔥
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-fire text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skills Progress -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Skills Progress</h2>
                    <p class="text-sm text-gray-500 mt-1">Track your development in each skill area</p>
                </div>
                <a href="{{ route('child.progress.skills') }}"
                   class="text-indigo-600 hover:text-indigo-800 text-sm">
                    View Details →
                </a>
            </div>

            @if($skillsProgress->isEmpty())
                <div class="text-center py-8">
                    <div class="text-5xl mb-3">🎯</div>
                    <p class="text-gray-500">Start learning to build your skills!</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach($skillsProgress as $skill)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="font-medium text-gray-900 capitalize">
                                    {{ str_replace('_', ' ', $skill['name']) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $skill['activities_count'] }} activities
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2.5 rounded-full transition-all"
                                     style="width: {{ min($skill['progress_percent'] ?? 0, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Impact Contributions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">🌍 Your Impact</h2>
                    <p class="text-sm text-gray-500 mt-1">Every lesson contributes to real-world projects</p>
                </div>
            </div>

            @if($impactContributions->isEmpty())
                <div class="text-center py-8">
                    <div class="text-5xl mb-3">💝</div>
                    <p class="text-gray-500">Keep learning to generate impact!</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($impactContributions as $contribution)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-hand-holding-heart text-pink-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $contribution->project->name ?? 'Learning contribution' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $contribution->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-pink-600">
                                    ${{ number_format($contribution->contribution_value, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 capitalize">
                                    {{ $contribution->status }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-8">
            <a href="{{ route('child.progress.activities') }}"
               class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition text-center">
                <i class="fas fa-history text-indigo-600 text-2xl mb-2"></i>
                <p class="font-medium">Activity Log</p>
            </a>
            <a href="{{ route('child.progress.skills') }}"
               class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition text-center">
                <i class="fas fa-chart-line text-green-600 text-2xl mb-2"></i>
                <p class="font-medium">Skill Report</p>
            </a>
            <a href="{{ route('child.dashboard') }}"
               class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition text-center">
                <i class="fas fa-home text-purple-600 text-2xl mb-2"></i>
                <p class="font-medium">Back to Dashboard</p>
            </a>
        </div>
    </div>
</div>
@endsection