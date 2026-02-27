@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Welcome Header with Stats --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    @switch($user->role)
                        @case('child')
                            Welcome back, {{ $user->name }}! 👋
                            @break
                        @case('parent')
                            Welcome back, {{ $user->name }} (Parent) 👨‍👩‍👧
                            @break
                        @case('tutor')
                            Welcome back, {{ $user->name }} (Tutor) 👩‍🏫
                            @break
                        @default
                            Welcome back! 👋
                    @endswitch
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ $ageGroup }} • {{ ucfirst($user->role) }} Account
                </p>
            </div>

            {{-- Quick Stats --}}
            <div class="mt-4 md:mt-0 flex space-x-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $context['total_learning_time'] }}m
                    </div>
                    <div class="text-xs text-gray-500">Time Learned</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $context['completed_count'] }}
                    </div>
                    <div class="text-xs text-gray-500">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ $context['current_streak'] }} 🔥
                    </div>
                    <div class="text-xs text-gray-500">Day Streak</div>
                </div>
            </div>
        </div>

        {{-- Progress to Next Milestone --}}
        @if($context['next_milestone'])
        <div class="mt-4 bg-gray-100 rounded-lg p-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="font-medium">
                    Next Milestone: {{ $context['next_milestone']['next'] }} items
                </span>
                <span class="text-gray-600">
                    {{ $context['next_milestone']['remaining'] }} more to go!
                </span>
            </div>
            <div class="w-full bg-gray-300 rounded-full h-2.5">
                <div class="bg-blue-600 rounded-full h-2.5 transition-all duration-500"
                     style="width: {{ $context['next_milestone']['progress'] }}%"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Continue Learning (Recent Activity) --}}
    @if($context['recent_activity']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="mr-2">🕒</span> Continue Learning
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @foreach($context['recent_activity'] as $activity)
                <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
                    <p class="font-medium text-sm truncate">{{ $activity['title'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- MAIN CONTENT SECTIONS --}}

    {{-- Section 1: Recommended for You --}}
    @if($content['recommended']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="mr-2">🎯</span> Recommended for You
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($content['recommended'] as $item)
                @include('partials.content-card', ['item' => $item])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section 2: Videos --}}
    @if($content['videos']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="mr-2">📺</span> Video Lessons
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($content['videos'] as $item)
                @include('partials.content-card', ['item' => $item])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section 3: Tutorials --}}
    @if($content['tutorials']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="mr-2">📚</span> Step-by-Step Tutorials
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($content['tutorials'] as $item)
                @include('partials.content-card', ['item' => $item])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section 4: Interactive Learning --}}
    @if($content['interactive']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="mr-2">🎮</span> Interactive Activities
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($content['interactive'] as $item)
                @include('partials.content-card', ['item' => $item])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section 5: Popular in Your Age Group --}}
    @if($content['popular']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="mr-2">🔥</span> Popular with {{ $ageGroup }}
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($content['popular'] as $item)
                @include('partials.mini-card', ['item' => $item])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section 6: Trending Now --}}
    @if($content['trending']->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="mr-2">📈</span> Trending Now
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($content['trending'] as $item)
                @include('partials.wide-card', ['item' => $item])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section 7: Based on Your Interests --}}
    @foreach($content['by_interest'] as $category => $items)
        @if($items->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-bold mb-4 flex items-center capitalize">
                <span class="mr-2">📌</span> More {{ $category }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($items as $item)
                    @include('partials.content-card', ['item' => $item])
                @endforeach
            </div>
        </div>
        @endif
    @endforeach

    {{-- Load More Button --}}
    <div class="text-center mt-8 pb-8">
        <button id="loadMoreBtn"
                onclick="loadMoreContent()"
                class="px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition shadow-sm">
            Load More Recommendations
        </button>
    </div>
</div>

@push('scripts')
<script>
let offset = 8;
let loading = false;

async function loadMoreContent() {
    if (loading) return;

    loading = true;
    const btn = document.getElementById('loadMoreBtn');
    btn.textContent = 'Loading...';
    btn.disabled = true;

    try {
        const response = await fetch(`/api/learning/more?offset=${offset}&limit=6`);
        const data = await response.json();

        if (data.success && data.html) {
            // Find the recommendations section
            const recommendationsSection = document.querySelector('[class*="Recommended for You"]').closest('.mb-10');
            recommendationsSection.insertAdjacentHTML('beforeend', data.html);
            offset += 6;

            if (!data.has_more) {
                btn.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Failed to load more content:', error);
    } finally {
        btn.textContent = 'Load More Recommendations';
        btn.disabled = false;
        loading = false;
    }
}

// Refresh button functionality
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        refreshContent();
    }
});

async function refreshContent() {
    const response = await fetch('/api/learning/refresh', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    if (response.ok) {
        window.location.reload();
    }
}
</script>
@endpush
@endsection
