{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('teams.layout.partials.head')
        <script>
            function layout() {
                return {
                    // State
                    sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
                    sidebarMobileOpen: false,
                    
                    // Navigation config
                    mainNav: [
                        {
                            icon: 'home',
                            text: 'Dashboard',
                            route: '/dashboard',
                            activePattern: '/dashboard'
                        },
                        {
                            icon: 'users',
                            text: '{{ __($teamDenomination['plural']) }}',
                            route: '{{ route($teamDenomination['plural_lower'] . '.index') }}',
                            activePattern: '/{{ $teamDenomination['plural_lower'] }}'
                        }
                    ],
                    
                    footerNav: [
                        {
                            icon: 'folder',
                            text: 'Repository',
                            route: 'https://github.com/laravel/livewire-starter-kit',
                            isExternal: true
                        },
                        {
                            icon: 'book-open',
                            text: 'Documentation',
                            route: 'https://laravel.com/docs/starter-kits#livewire',
                            isExternal: true
                        }
                    ],
                    
                    user: {
                        name: '{{ auth()->user()->name ?? "Mario Nobre" }}',
                        email: '{{ auth()->user()->email ?? "m.nobre@ymail.com" }}',
                        initials: '{{ auth()->user() ? implode("", array_map(fn($name) => $name[0], explode(" ", auth()->user()->name))) : "MN" }}'
                    },
                    
                    // Methods
                    toggleSidebar() {
                        if (window.innerWidth < 1024) {
                            this.sidebarMobileOpen = !this.sidebarMobileOpen;
                        } else {
                            this.sidebarCollapsed = !this.sidebarCollapsed;
                            localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
                        }
                    },
                    
                    isActive(link) {
                        if (link.activePattern) {
                            return window.location.pathname === link.route || 
                                window.location.pathname.startsWith(link.activePattern);
                        }
                        return window.location.pathname === link.route;
                    },
                    
                    // Handle resize events
                    handleResize() {
                        if (window.innerWidth >= 1024) {
                            this.sidebarMobileOpen = false;
                        }
                    },
                    
                    // Initialize
                    initLayout() {
                        // Add resize listener
                        window.addEventListener('resize', () => this.handleResize());
                        
                        // Clean up on destroy
                        this.$watch('sidebarMobileOpen', (value) => {
                            if (value) {
                                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
                            } else {
                                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
                            }
                        });
                    }
                }
            }
        </script>
        @livewireStyles
    </head>
<body class="min-h-screen bg-white dark:bg-zinc-800 overflow-hidden antialiased"
      x-data="layout()"
      x-init="initLayout"
      @sidebar-toggle.window="toggleSidebar()"
      :class="{ 'sidebar-collapsed': sidebarCollapsed }">
    
    {{-- Sidebar Backdrop (mobile) with fade transition --}}
    <div x-show="sidebarMobileOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarMobileOpen = false"
         class="fixed inset-0 z-30 bg-black/20 backdrop-blur-sm lg:hidden">
    </div>

    {{-- Main Container --}}
    <div class="flex h-screen w-full overflow-hidden">
        {{-- Sidebar --}}

        @include('teams.layout.partials.sidebar')

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col h-full overflow-hidden bg-white dark:bg-zinc-800 transition-colors duration-300">
            @include('teams.layout.partials.content-header')
            {{-- Main Content with smooth transitions --}}
            <main class="flex-1 overflow-y-auto p-6 lg:p-8 scroll-smooth">
                @yield('content')
            </main>
        </div>
    </div>



    @livewireScripts
</body>
</html>