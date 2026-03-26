{{-- resources/views/teams/pending-invitations.blade.php --}}
@extends('teams.teams')

@section('teams-content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        Pending Invitations
                    </h2>
                    <a href="{{ route('teams.show', $team) }}" 
                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        ← Back to {{ $teamDenomination['singular'] }}
                    </a>
                </div>

                @if($invitations->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-zinc-900 dark:text-white">
                            No pending invitations
                        </h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                            All invitations have been accepted or declined.
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($invitations as $invitation)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 hover:bg-zinc-50 dark:hover:bg-zinc-750 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-white font-bold">
                                                    {{ strtoupper(substr($invitation->email, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-zinc-900 dark:text-white">
                                                {{ $invitation->email }}
                                            </h4>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    Pending
                                                </span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    Role: {{ ucfirst($invitation->role) }}
                                                </span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    Sent: {{ $invitation->created_at->diffForHumans() }}
                                                </span>
                                                @if($invitation->expires_at)
                                                    <span class="text-xs {{ $invitation->expires_at->isPast() ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                                        Expires: {{ $invitation->expires_at->format('M j, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($invitation->message)
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 italic">
                                                    "{{ $invitation->message }}"
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <form action="{{ route('team.invitations.resend', $invitation) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" 
                                                    class="px-3 py-1 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 border border-blue-300 dark:border-blue-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                                Resend
                                            </button>
                                        </form>
                                        <form action="{{ route('team.invitations.cancel', $invitation) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1 text-sm text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 border border-rose-300 dark:border-rose-600 rounded hover:bg-rose-50 dark:hover:bg-rose-900/20 transition"
                                                    onclick="return confirm('Are you sure you want to cancel this invitation?');">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection