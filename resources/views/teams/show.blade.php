@extends('teams.layout.main')
@section('content')
    <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route($teamDenomination['plural_lower'] . '.index') }}" 
                   class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                    <svg class="w-5 h-5 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $team->name }}
                    </h2>
                    @if($team->personal_team)
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                            Personal {{ $teamDenomination['singular_lower'] }}
                        </p>
                    @endif
                </div>
            </div>
            
            @can('update', $team)
            <div class="flex items-center space-x-3">
                <a href="{{ route($teamDenomination['plural_lower'] . '.edit', $team) }}" 
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-lg font-medium text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-zinc-800 transition">
                    Edit
                </a>
                @unless($team->personal_team)
                    @can('delete', $team)
                    <form method="POST" action="{{ route($teamDenomination['plural_lower'] . '.destroy', $team) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this {{ strtolower($teamDenomination['singular']) }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 dark:focus:ring-offset-zinc-800 transition">
                            Delete
                        </button>
                    </form>
                    @endcan
                @endunless
            </div>
            @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Team Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Team Members -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ $teamDenomination['singular'] }} Members
                    </h3>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($team->users as $user)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-700 transition">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="font-semibold ">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-zinc-900 dark:text-white">
                                        {{ $user->name }}
                                        @if($user->id === $team->user_id)
                                            <span class="ml-2 text-xs font-medium px-2 py-1 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                                Owner
                                            </span>
                                        @endif
                                    </h4>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </div>
                            
                            @can('updateTeamMember', $team)
                                @if($user->id !== auth()->id() && $user->id !== $team->user_id)
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ ucfirst($user->pivot->role) }}
                                    </span>
                                    <form method="POST" 
                                          action="{{ route($teamDenomination['plural_lower'] . '.members.destroy', [$team, $user]) }}"
                                          onsubmit="return confirm('Remove {{ $user->name }} from this {{ strtolower($teamDenomination['singular']) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-sm text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                                @endif
                            @endcan
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Info -->
        <div class="space-y-6">
            <!-- Invite Member Form -->
            @can('addTeamMember', $team)
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                        Invite Member
                    </h3>
                    <form method="POST" action="{{ route($teamDenomination['plural_lower'] . '.members.store', $team) }}">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Email Address
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       required
                                       class="block w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 transition"
                                       placeholder="user@example.com">
                            </div>
                            
                            <div>
                                <label for="role" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Role
                                </label>
                                <select name="role" 
                                        id="role"
                                        class="block w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 transition">
                                    @foreach(config('teams.roles') as $key => $label)
                                        @if($key !== 'owner')
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition">
                                Invite to {{ $teamDenomination['singular'] }}
                            </button>
                        </div>
                    </form>
                </div>
            @endcan

            <!-- Team Information -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                    {{ $teamDenomination['singular'] }} Information
                </h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                            Owner
                        </dt>
                        <dd class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $team->owner->name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                            Created
                        </dt>
                        <dd class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $team->created_at->format('M d, Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                            Members
                        </dt>
                        <dd class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $team->users->count() }} people
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-3">
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    @if($team->id !== auth()->user()->current_team_id)
                        <form method="POST" action="{{ route($teamDenomination['plural_lower'] . '.switch', $team) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" 
                                    class="w-full flex items-center justify-between px-4 py-3 bg-white dark:bg-blue-800/30 border border-blue-200 dark:border-blue-700 rounded-lg text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-800/40 transition group">
                                <span class="font-medium">Switch to this {{ $teamDenomination['singular_lower'] }}</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route($teamDenomination['plural_lower'] . '.edit', $team) }}" 
                       class="block px-4 py-3 bg-white dark:bg-blue-800/30 border border-blue-200 dark:border-blue-700 rounded-lg text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-800/40 transition text-center font-medium">
                        Edit {{ $teamDenomination['singular_lower'] }} Details
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection