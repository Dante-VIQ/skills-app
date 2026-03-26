            {{-- Mobile Header with smooth blur --}}
            <header class="lg:hidden flex items-center min-h-16 px-4 bg-white/80 dark:bg-zinc-800/80 backdrop-blur-md border-b border-zinc-200/50 dark:border-zinc-700/50 flex-shrink-0 sticky top-0 z-20">
                <button @click="sidebarMobileOpen = true" 
                        class="flex items-center justify-center h-10 w-10 -ml-2 rounded-lg bg-transparent hover:bg-zinc-800/10 dark:hover:bg-white/15 text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white transition-colors">
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2 6.75A.75.75 0 0 1 2.75 6h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 6.75Zm0 6.5a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <div class="flex-1 flex justify-center">
                    <span class="text-sm font-semibold text-zinc-800 dark:text-white">Dashboard</span>
                </div>

                {{-- Mobile User Menu --}}
                <div x-data="{ open: false }" 
                     @keydown.escape.window="open = false"
                     class="relative">
                    <button @click="open = !open" 
                            @click.away="open = false"
                            class="flex items-center rounded-lg p-1.5 hover:bg-zinc-800/10 dark:hover:bg-white/15 transition-colors group">
                        <div class="size-8 flex items-center justify-center rounded-md bg-gradient-to-br from-zinc-200 to-zinc-300 dark:from-zinc-600 dark:to-zinc-700 text-zinc-800 dark:text-white text-sm font-medium group-hover:scale-105 transition-transform">
                            <span x-text="user.initials"></span>
                        </div>
                    </button>

                    {{-- Mobile Dropdown with smooth transition --}}
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 p-1.5 rounded-xl shadow-lg border border-zinc-200/50 dark:border-zinc-700/50 bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm focus:outline-hidden z-50">
                        
                        <div class="flex items-center gap-3 px-2 py-2 text-sm">
                            <div class="size-10 flex items-center justify-center rounded-lg bg-gradient-to-br from-zinc-200 to-zinc-300 dark:from-zinc-600 dark:to-zinc-700 text-zinc-800 dark:text-white font-medium">
                                <span x-text="user.initials" class="text-base"></span>
                            </div>
                            <div class="grid flex-1 text-start leading-tight">
                                <div class="font-semibold text-zinc-800 dark:text-white text-sm truncate"
                                     x-text="user.name">
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate"
                                   x-text="user.email">
                                </p>
                            </div>
                        </div>
                        
                        <div class="-mx-1 my-1 h-px bg-zinc-200/70 dark:bg-zinc-700/70"></div>
                        
                        <a href="{{ url('/settings/profile') }}" 
                           class="flex items-center px-2 py-2 w-full rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/70 transition-colors group"
                           wire:navigate>
                            <svg class="shrink-0 size-5 me-2 text-zinc-400 dark:text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-300" 
                                 xmlns="http://www.w3.org/2000/svg" 
                                 viewBox="0 0 20 20" 
                                 fill="currentColor">
                                <path fill-rule="evenodd" d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.25l-1.267 1.113a7.047 7.047 0 0 1 0 2.23l1.267 1.113a1 1 0 0 1 .205 1.25l-1.18 2.044a1 1 0 0 1-1.186.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.25l1.267-1.113a7.047 7.047 0 0 1 0-2.23L1.82 7.593a1 1 0 0 1-.205-1.25l1.18-2.044a1 1 0 0 1 1.186-.447l1.598.54A6.993 6.993 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                            </svg>
                            Settings
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center px-2 py-2 w-full rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/70 transition-colors group cursor-pointer">
                                <svg class="shrink-0 size-5 me-2 text-zinc-400 dark:text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-300" 
                                     xmlns="http://www.w3.org/2000/svg" 
                                     viewBox="0 0 20 20" 
                                     fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M6 10a.75.75 0 0 1 .75-.75h9.546l-1.048-.943a.75.75 0 1 1 1.004-1.114l2.5 2.25a.75.75 0 0 1 0 1.114l-2.5 2.25a.75.75 0 1 1-1.004-1.114l1.048-.943H6.75A.75.75 0 0 1 6 10Z" clip-rule="evenodd"/>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </header>
