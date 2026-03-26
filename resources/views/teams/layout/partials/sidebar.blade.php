        <aside 
            :class="[
                'h-full flex flex-col gap-4 border-e border-zinc-200 bg-zinc-50/95 dark:border-zinc-700 dark:bg-zinc-900/95 backdrop-blur-sm transition-all duration-300 ease-out flex-shrink-0',
                sidebarCollapsed ? 'lg:w-14 lg:px-2' : 'lg:w-64 p-4',
                sidebarMobileOpen ? 'translate-x-0 shadow-xl' : '-translate-x-full lg:translate-x-0',
                'fixed lg:relative z-40'
            ]"
            class="rtl:left-auto rtl:right-0 rtl:translate-x-full lg:rtl:translate-x-0"
            :class="{ 'rtl:translate-x-0': sidebarMobileOpen }"
        >
            {{-- Sidebar Header --}}
            <div class="flex items-center justify-between gap-2 min-h-10">
                <a href="{{ url('https://codeberg.org/mnobre/mnobre') }}" 
                   :class="[
                       'h-10 flex items-center gap-2 transition-all duration-300',
                       sidebarCollapsed ? 'lg:w-10 lg:px-0 lg:justify-center' : 'px-2'
                   ]"
                   target="_blank" rel="noopener noreferrer"
                   class="group">
                    <div class="flex items-center justify-center size-9 shrink-0 rounded-md bg-accent-content text-accent-foreground overflow-hidden transition-transform group-hover:scale-105">
                        <img src="https://codeberg.org/mnobre/mnobre/raw/branch/main/media/LT-128.png" 
                             alt="Laravel Teams"
                             class="w-full h-full object-cover">
                    </div>
                    
                    <div x-show="!sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-x-2"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-2"
                         class="text-sm font-medium truncate text-zinc-800 dark:text-zinc-100 lg:block">
                        <div class="grid grid-rows-2 grid-cols-1">
                            <div class="font-semibold">Laravel Teams</div>
                            <div class="text-[8px] leading-tight text-zinc-500 dark:text-zinc-400">
                                <span>by</span> 
                                <span class="font-medium text-zinc-600 dark:text-zinc-300">mnobre</span>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Close Sidebar button (mobile) --}}
                <button @click="sidebarMobileOpen = false"
                        class="lg:hidden w-10 h-8 flex items-center justify-center rounded-lg bg-transparent hover:bg-zinc-800/10 dark:hover:bg-white/15 text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white transition-colors">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            {{-- Sidebar Navigation --}}
            <nav class="flex flex-col overflow-y-auto flex-1 scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-700">
                <div class="space-y-[2px]">
                    {{-- Platform Section --}}
                    <div x-show="!sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="px-3 py-2">
                        <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                            Platform
                        </div>
                    </div>

                    {{-- MAIN NAVIGATION --}}
                    <template x-for="link in mainNav" :key="link.route">
                        <a :href="link.route" 
                           :class="[
                               'relative flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all duration-200 group',
                               'text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white',
                               'hover:bg-zinc-200/50 dark:hover:bg-white/[8%]',
                               isActive(link) ? 'bg-white dark:bg-white/[8%] border border-zinc-200 dark:border-white/5 text-accent-content shadow-sm' : '',
                               sidebarCollapsed ? 'lg:w-10 lg:justify-center lg:mx-auto' : ''
                           ]"
                           :target="link.isExternal ? '_blank' : '_self'"
                           :rel="link.isExternal ? 'noopener noreferrer' : ''"
                           wire:navigate>
                            
                            <div class="relative shrink-0 size-5" 
                                 :class="{ 'lg:mx-auto': sidebarCollapsed }">
                                <img :src="`https://raw.githubusercontent.com/tailwindlabs/heroicons/refs/heads/master/src/24/outline/${link.icon}.svg`" 
                                     class="size-5 dark:invert transition-transform group-hover:scale-110"
                                     :alt="link.icon"
                                     loading="lazy">
                                
                                {{-- Active indicator --}}
                                <span x-show="isActive(link) && sidebarCollapsed" 
                                      class="absolute -left-1 top-1/2 -translate-y-1/2 w-1 h-5 bg-accent-content rounded-full lg:block hidden">
                                </span>
                            </div>
                            
                            <span x-show="!sidebarCollapsed" 
                                  x-transition:enter="transition ease-out duration-300"
                                  x-transition:enter-start="opacity-0 -translate-x-2"
                                  x-transition:enter-end="opacity-100 translate-x-0"
                                  class="flex-1 text-sm font-medium truncate"
                                  x-text="link.text">
                            </span>
                            
                            <span x-show="!sidebarCollapsed && link.isExternal" 
                                  x-transition:enter="transition ease-out duration-300"
                                  class="shrink-0 size-3.5 opacity-60 group-hover:opacity-100">
                                <img class="dark:invert" 
                                     src="https://raw.githubusercontent.com/tailwindlabs/heroicons/refs/heads/master/src/24/outline/arrow-top-right-on-square.svg" 
                                     class="size-3.5"
                                     alt="external">
                            </span>
                        </a>
                    </template>
                </div>
            </nav>

            {{-- Spacer --}}
            <div class="flex-1"></div>

            {{-- Footer Navigation --}}
            <nav class="flex flex-col space-y-[2px]">
                <template x-for="link in footerNav" :key="link.route">
                    <a :href="link.route" 
                       :class="[
                           'relative flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all duration-200 group',
                           'text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white',
                           'hover:bg-zinc-200/50 dark:hover:bg-white/[8%]',
                           sidebarCollapsed ? 'lg:w-10 lg:justify-center lg:mx-auto' : ''
                       ]"
                       target="_blank"
                       rel="noopener noreferrer"
                       wire:navigate>
                        
                        <div class="relative shrink-0 size-5" 
                             :class="{ 'lg:mx-auto': sidebarCollapsed }">
                            <img :src="`https://raw.githubusercontent.com/tailwindlabs/heroicons/refs/heads/master/src/24/outline/${link.icon}.svg`" 
                                 class="size-5 dark:invert transition-transform group-hover:scale-110"
                                 :alt="link.icon"
                                 loading="lazy">
                        </div>
                        
                        <span x-show="!sidebarCollapsed" 
                              x-transition:enter="transition ease-out duration-300"
                              x-transition:enter-start="opacity-0 -translate-x-2"
                              x-transition:enter-end="opacity-100 translate-x-0"
                              class="flex-1 text-sm font-medium truncate"
                              x-text="link.text">
                        </span>
                    </a>
                </template>
            </nav>

            {{-- User Menu (Desktop) --}}
            <div x-show="!sidebarCollapsed" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="lg:block hidden mt-2">
                <div x-data="{ open: false }" 
                     @keydown.escape.window="open = false"
                     class="relative">
                    <button @click="open = !open" 
                            @click.away="open = false"
                            class="w-full flex items-center rounded-lg p-1.5 hover:bg-zinc-200/50 dark:hover:bg-white/[8%] transition-all duration-200 group">
                        <div class="shrink-0">
                            <div class="size-8 flex items-center justify-center rounded-md bg-gradient-to-br from-zinc-200 to-zinc-300 dark:from-zinc-600 dark:to-zinc-700 text-zinc-800 dark:text-white text-sm font-medium group-hover:scale-105 transition-transform">
                                <span x-text="user.initials"></span>
                            </div>
                        </div>
                        
                        <span class="mx-2 text-sm font-medium truncate flex-1 text-left text-zinc-700 dark:text-zinc-200 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors"
                              x-text="user.name">
                        </span>
                        
                        <div class="shrink-0 size-8 flex justify-center items-center">
                            <svg class="size-4 text-zinc-400 dark:text-zinc-500 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 xmlns="http://www.w3.org/2000/svg" 
                                 viewBox="0 0 24 24" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 stroke-width="2">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </button>

                    {{-- Dropdown Menu with smooth transition --}}
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="absolute bottom-full left-0 mb-2 w-56 p-1.5 rounded-xl shadow-lg border border-zinc-200/50 dark:border-zinc-700/50 bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm focus:outline-hidden">
                        
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
                            <div class="shrink-0 size-5 me-2 text-zinc-400 dark:text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-300">
                                <img src="https://raw.githubusercontent.com/tailwindlabs/heroicons/refs/heads/master/src/24/outline/cog-6-tooth.svg" 
                                     class="size-5 dark:invert"
                                     alt="settings">
                            </div>
                            Settings
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center px-2 py-2 w-full rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/70 transition-colors group cursor-pointer">
                                <div class="shrink-0 size-5 me-2 text-zinc-400 dark:text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-300">
                                    <img src="https://raw.githubusercontent.com/tailwindlabs/heroicons/refs/heads/master/src/24/outline/arrow-right-on-rectangle.svg" 
                                         class="size-5 dark:invert"
                                         alt="logout">
                                </div>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>