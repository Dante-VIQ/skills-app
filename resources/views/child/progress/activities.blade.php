@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('child.progress') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                &larr; Back to Progress
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Activity Log 📅</h1>
            <p class="text-gray-600 mt-1">Everything you've been up to.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            @if(isset($activities) && $activities->isNotEmpty())
                <div class="space-y-4">
                    @foreach($activities as $activity)
                        <div class="flex items-start space-x-3 pb-4 border-b last:border-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-indigo-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $activity->title }}</p>
                                <p class="text-sm text-gray-600">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $activity->created_at->format('M d, Y g:i A') }}
                                </p>
                            </div>
                            <div>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 capitalize">
                                    {{ $activity->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $activities->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📖</div>
                    <p class="text-gray-500">No activity yet. Start learning to build your log!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection