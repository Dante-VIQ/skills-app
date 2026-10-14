@extends('layouts.app')

@section('title', 'Skill Report')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('child.progress') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                &larr; Back to Progress
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Skill Report 🎯</h1>
            <p class="text-gray-600 mt-1">Your strengths and growth areas.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
@if(!empty($skills))
    <div class="space-y-6">
        @foreach($skills as $skill)
            <div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold capitalize">{{ str_replace('_', ' ', $skill['name']) }}</span>
                    <span class="text-sm text-gray-500">{{ $skill['level'] ?? 'Beginner' }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full"
                         style="width: {{ $skill['progress_percent'] ?? 0 }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $skill['activities_count'] ?? 0 }} activities completed
                </p>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-12">
        <div class="text-6xl mb-4">🌟</div>
        <p class="text-gray-500">Complete lessons to build your skills!</p>
    </div>
@endif
        </div>
    </div>
</div>
@endsection