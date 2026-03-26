@extends('teams.layout.main')
@section('content')
    <div>
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route($teamDenomination['plural_lower'] . '.index') }}" 
               class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                <svg class="w-5 h-5 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                New {{ $teamDenomination['singular'] }}
            </h2>
        </div>

        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-zinc-800 shadow-sm border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route($teamDenomination['plural_lower'] . '.store') }}">
                        @csrf

                        <div class="space-y-6">
                            <!-- Team Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    {{ $teamDenomination['singular'] }} Name
                                </label>
                                <input type="text" 
                                    name="name" 
                                    id="name" 
                                    value="{{ old('name') }}"
                                    required
                                    autofocus
                                    class="block w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 transition"
                                    placeholder="Enter {{ strtolower($teamDenomination['singular']) }} name">
                                @error('name')
                                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Personal Team Note -->
                            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                            About {{ $teamDenomination['plural'] }}
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                            <p>
                                                {{ $teamDenomination['singular'] == 'Team' ? 'Teams' : $teamDenomination['plural'] }} allow you to collaborate with multiple users on shared resources.
                                                Each user already has a personal {{ strtolower($teamDenomination['singular']) }} for individual work.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                                <a href="{{ route($teamDenomination['plural_lower'] . '.index') }}" 
                                class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg font-medium text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-zinc-800 transition">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-zinc-800 transition">
                                    Create {{ $teamDenomination['singular'] }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection