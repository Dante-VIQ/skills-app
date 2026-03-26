@extends('teams.layout.main')
@section('content')
    <div name="header">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                My {{ $teamDenomination['plural'] }}
            </h2>
            <a href="{{ route($teamDenomination['plural_lower'] . '.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150">
                New {{ $teamDenomination['singular'] }}
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Current Team -->
        @if($currentTeam)
            <div class="bg-gradient-to-br from-white to-zinc-50 dark:from-zinc-800 dark:to-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-bold text-lg">
                                    {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                    {{ $currentTeam->name }}
                                    @if($currentTeam->personal_team)
                                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                            Personal
                                        </span>
                                    @endif
                                </h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $currentTeam->owner->name }} • {{ $currentTeam->users->count() }} {{ $teamDenomination['singular'] == 'Team' ? 'members' : strtolower($teamDenomination['singular']) . ' members' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route($teamDenomination['plural_lower'] . '.show', $currentTeam) }}" 
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-md font-medium text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-zinc-800 transition">
                            View {{ $teamDenomination['singular'] }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
        @if($teams->count() > 0)
            <!-- All Teams Grid -->
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                    All {{ $teamDenomination['plural'] }}
                </h3>            
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($teams as $team)
                        @if(!$currentTeam || $team->id != $currentTeam->id)
                            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-r {{ $team->personal_team ? 'from-blue-500 to-blue-600' : 'from-purple-500 to-pink-600' }} flex items-center justify-center">
                                                <span class="text-white font-bold text-xl">
                                                    {{ strtoupper(substr($team->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-zinc-900 dark:text-white">
                                                    {{ $team->name }}
                                                    @if($team->personal_team)
                                                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                            Personal
                                                        </span>
                                                    @endif
                                                </h4>
                                                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                                    {{ $team->owner->name }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($team->pivot && $team->pivot->role)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $team->pivot->role == 'owner' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-300' }}">
                                                {{ ucfirst($team->pivot->role) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-6 flex items-center justify-between">
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $team->users->count() }} members
                                        </span>
                                        <div class="flex items-center space-x-2">
                                            @if($currentTeam && $currentTeam->id !== $team->id)
                                                <form method="POST" action="{{ route($teamDenomination['plural_lower'] . '.switch', $team) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                                        Switch
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <a href="{{ route($teamDenomination['plural_lower'] . '.show', $team) }}" 
                                            class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-300 font-medium">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @elseif ($teams->count() < 1)
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-zinc-300 dark:text-zinc-700">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-zinc-900 dark:text-white">
                    No {{ $teamDenomination['plural'] }} yet
                </h3>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto">
                    Get started by creating your first {{ strtolower($teamDenomination['singular']) }}. 
                    {{ $teamDenomination['singular'] == 'Team' ? 'Teams' : $teamDenomination['plural'] }} help you collaborate with others.
                </p>
                <div class="mt-6">
                    <a href="{{ route($teamDenomination['plural_lower'] . '.create') }}" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800">
                        Create {{ $teamDenomination['singular'] }}
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection