@extends('teams.layout.main')
@section('content')
    <div name="header">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route($teamDenomination['plural_lower'] . '.show', $team) }}" 
            class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                <svg class="w-5 h-5 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                Edit {{ $teamDenomination['singular'] }}
            </h2>
        </div>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 shadow-sm border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route($teamDenomination['plural_lower'] . '.update', $team) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Team Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                {{ $teamDenomination['singular'] }} Name
                            </label>
                            <input type="text" 
                                name="name" 
                                id="name" 
                                value="{{ old('name', $team->name) }}"
                                required
                                autofocus
                                class="block w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 transition"
                                placeholder="Enter {{ strtolower($teamDenomination['singular']) }} name">
                            @error('name')
                                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Team Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                {{ $teamDenomination['singular'] }} Slug
                            </label>
                            <input type="text" 
                                name="slug" 
                                id="slug" 
                                value="{{ old('slug', $team->slug) }}"
                                required
                                autofocus
                                class="block w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 transition"
                                placeholder="{{ strtolower($teamDenomination['singular']) }} sluge">
                            @error('slug')
                                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                            <a href="{{ route($teamDenomination['plural_lower'] . '.show', $team) }}" 
                            class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg font-medium text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-zinc-800 transition">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-zinc-800 transition">
                                Save Changes
                            </button>
                        </div>


                    
                    </div>
                </form>
                                        <!-- Danger Zone (for non-personal teams) -->
                        @unless($team->personal_team)
                            @can('delete', $team)
                            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6 mt-6">
                                <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-rose-900 dark:text-rose-200 mb-2">
                                        Danger Zone
                                    </h3>
                                    <p class="text-sm text-rose-700 dark:text-rose-300 mb-4">
                                        Deleting this {{ strtolower($teamDenomination['singular']) }} is permanent and cannot be undone. 
                                        All members will lose access and all {{ strtolower($teamDenomination['singular']) }} data will be removed.
                                    </p>
                                    <form method="POST" 
                                        action="{{ route($teamDenomination['plural_lower'] . '.destroy', $team) }}" 
                                        onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 dark:focus:ring-offset-zinc-800 transition">
                                            Delete {{ $teamDenomination['singular'] }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endcan
                        @endunless

            </div>
        </div>
    </div>
@endsection