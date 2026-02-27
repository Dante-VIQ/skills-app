{{-- resources/views/learning/universal.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    {{-- Header - Same layout, different greeting --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">
                    @if($user->role === 'child')
                        Welcome back, {{ $user->name }}! 👋
                    @elseif($user->role === 'parent')
                        Welcome back, {{ $user->name }} (Parent) 👨‍👩‍👧
                    @elseif($user->role === 'tutor')
                        Welcome back, {{ $user->name }} (Tutor) 👩‍🏫
                    @endif
                </h1>
                <p class="text-gray-600 mt-2">
                    {{ $ageGroup }} • {{ ucfirst($user->role) }} Account
                </p>
            </div>
            
            {{-- Quick Stats --}}
            <div class="flex space-x-4">
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ round($context['total_learning_time']) }}h
                    </div>
                    <div class="text-xs text-gray-500">Total Time</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $context['completed_count'] }}
                    </div>
                    <div class="text-xs text-gray-500">Completed</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ $context['current_streak'] }} 🔥
                    </div>
                    <div class="text-xs text-gray-500">Day Streak</div>
                </div>
            </div>
        </div>
        
        {{-- Progress to next milestone --}}
        @if($context['next_milestone'])
        <div class="mt-4 bg-gray-100 rounded-lg p-4">
            <div class="flex justify-between text-sm mb-1">
                <span>Next Milestone: {{ $context['next_milestone']['next'] }} items</span>
                <span>{{ $context['next_milestone']['remaining'] }} more to go!</span>
            </div>
            <div class="w-full bg-gray-300 rounded-full h-2">
                <div class="bg-blue-600 rounded-full h-2" 
                     style="width: {{ $context['next_milestone']['progress'] }}%"></div>
            </div>
        </div>
        @endif
    </div>
    
    {{-- Continue Learning (Recent Activity) --}}
    @if($context['recent_activity']->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4">🕒 Continue Learning</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @foreach($context['recent_activity'] as $activity)
                <div class="bg-white rounded-lg shadow p-3 hover:shadow-md">
                    <p class="font-medium text-sm truncate">{{ $activity['title'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- MAIN CONTENT SECTIONS - Same for everyone! --}}
    
    {{-- Section 1: Recommended for You --}}
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4">🎯 Recommended for You</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($content['recommended'] as $item)
                @include('partials.content-card', ['item' => $item, 'size' => 'normal'])
            @endforeach
        </div>
    </div>
    
    {{-- Section 2: Based on Your Interests --}}
    @foreach($content['based_on_interests'] as $category => $items)
        @if($items->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-bold mb-4 capitalize">
                📚 More {{ $category }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($items as $item)
                    @include('partials.content-card', ['item' => $item, 'size' => 'small'])
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
    
    {{-- Section 3: Popular in Your Age Group --}}
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4">🔥 Popular with {{ $ageGroup }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($content['popular_in_age'] as $item)
                @include('partials.content-card', ['item' => $item, 'size' => 'mini'])
            @endforeach
        </div>
    </div>
    
    {{-- Section 4: Trending Now --}}
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4">📈 Trending Now</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($content['trending'] as $item)
                @include('partials.content-card', ['item' => $item, 'size' => 'wide'])
            @endforeach
        </div>
    </div>
    
    {{-- Section 5: Fill Your Skill Gaps --}}
    @if($content['skill_gaps']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4">🚀 New Skills to Explore</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($content['skill_gaps'] as $item)
                @include('partials.content-card', ['item' => $item, 'size' => 'normal'])
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Parent-Specific Section (only shows for parents) --}}
    @if($user->role === 'parent' && isset($content['for_parents']))
    <div class="mb-10 bg-blue-50 p-6 rounded-lg">
        <h2 class="text-xl font-bold mb-4">👨‍👩‍👧 Parenting Resources</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($content['for_parents'] as $item)
                @include('partials.content-card', ['item' => $item, 'size' => 'normal'])
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Tutor-Specific Section (only shows for tutors) --}}
    @if($user->role === 'tutor' && isset($content['teaching_materials']))
    <div class="mb-10 bg-green-50 p-6 rounded-lg">
        <h2 class="text-xl font-bold mb-4">📋 Teaching Materials</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($content['teaching_materials'] as $item)
                @include('partials.content-card', ['item' => $item, 'size' => 'normal'])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection