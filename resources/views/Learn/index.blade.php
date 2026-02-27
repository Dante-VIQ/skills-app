{{-- resources/views/Learn/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Welcome Header --}}
    <div class="mb-8">
        {{-- FIXED: Check if variables exist before using them --}}
        <h1 class="text-3xl font-bold text-gray-900">
            Welcome back, 
            @if(isset($child) && $child)
                {{ $child->name }}! 👋
            @elseif(isset($user) && $user)
                {{ $user->name }}! 👋
            @else
                Learner! 👋
            @endif
        </h1>
        <p class="text-gray-600 mt-2">Ready to learn something new today?</p>
    </div>
    
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        {{-- FIXED: Check if stats exists before using --}}
        @if(isset($stats))
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_watched'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Videos Watched</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-green-600">{{ floor($stats['total_minutes'] ?? 0) }}</div>
            <div class="text-sm text-gray-600">Minutes Learned</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['completed_count'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Completed</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['points'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Points Earned</div>
        </div>
        @else
        {{-- Fallback stats if none provided --}}
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">0</div>
            <div class="text-sm text-gray-600">Videos Watched</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-green-600">0</div>
            <div class="text-sm text-gray-600">Minutes Learned</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-purple-600">0</div>
            <div class="text-sm text-gray-600">Completed</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600">0</div>
            <div class="text-sm text-gray-600">Points Earned</div>
        </div>
        @endif
    </div>
    
    {{-- Current Assignments --}}
    @if(isset($assignments) && $assignments->count() > 0)
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4">📋 Your Assignments</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($assignments as $assignment)
            <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-start">
                    <div class="flex-1">
                        <h3 class="font-semibold">{{ $assignment->resource->title ?? 'Assignment' }}</h3>
                        <p class="text-sm text-gray-600">{{ Str::limit($assignment->resource->description ?? '', 100) }}</p>
                        <div class="mt-2">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Progress</span>
                                <span>{{ $assignment->progress_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 rounded-full h-2" 
                                     style="width: {{ $assignment->progress_percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('learning.show', $assignment->resource->id ?? 0) }}" 
                       class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        Continue
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Personalized Recommendations --}}
    @if(isset($recommendations) && count($recommendations) > 0)
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4">🎯 Recommended for You</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="recommendations-grid">
            @foreach($recommendations as $rec)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition recommendation-card">
                <div class="relative pb-48 overflow-hidden">
                    @if($rec['thumbnail'] ?? false)
                        <img class="absolute inset-0 w-full h-full object-cover" 
                             src="{{ $rec['thumbnail'] }}" 
                             alt="{{ $rec['title'] }}">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-500 
                                    flex items-center justify-center text-white text-4xl">
                            {{ substr($rec['title'] ?? 'L', 0, 1) }}
                        </div>
                    @endif
                    
                    <span class="absolute top-2 right-2 px-2 py-1 bg-black bg-opacity-50 
                                 text-white text-xs rounded">
                        {{ $rec['estimated_minutes'] ?? 5 }} min
                    </span>
                </div>
                
                <div class="p-4">
                    <div class="flex items-center mb-2">
                        <span class="text-xs font-semibold px-2 py-1 bg-blue-100 text-blue-800 rounded">
                            {{ $rec['category'] ?? 'Learning' }}
                        </span>
                        <span class="ml-2 text-xs text-gray-500">
                            {{ ucfirst($rec['difficulty'] ?? 'beginner') }}
                        </span>
                    </div>
                    
                    <h3 class="font-bold text-lg mb-1">{{ $rec['title'] ?? 'Learning Resource' }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($rec['description'] ?? '', 80) }}</p>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 italic">
                            {{ $rec['reason'] ?? 'Recommended for you' }}
                        </span>
                        <a href="{{ route('learning.show', $rec['id'] ?? 0) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Start →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection