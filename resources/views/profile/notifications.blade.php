@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-1">View your recent notifications.</p>
            </div>
            @if($notifications->whereNull('read_at')->count() > 0)
                <form method="POST" action="{{ route('profile.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-sm">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($notifications->isEmpty())
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🔔</div>
                    <h3 class="text-lg font-medium text-gray-900">No notifications yet</h3>
                    <p class="text-gray-500 mt-1">You'll see updates here when things happen.</p>
                </div>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        <li class="p-4 {{ $notification->read_at ? 'bg-white' : 'bg-indigo-50' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('profile.notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-xs">
                                            Mark read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="px-4 py-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection