<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SkillDNA') - Where Learning Builds Futures</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />

    @livewireStyles
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .skilldna-card {
            transition: all 0.3s ease;
            border-radius: 1rem;
            overflow: hidden;
        }

        .skilldna-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 min-h-full">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-xl">S</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">SkillDNA</span>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm focus:outline-none">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="hidden md:inline text-gray-700">{{ auth()->user()->getDisplayName() }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                @if (auth()->user()->isParent())
                                    <a href="{{ route('parent.dashboard') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <a href="{{ route('parent.add-child') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add Child</a>
                                    <a href="{{ route('parent.assignments') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Assignments</a>
                                @else
                                    <a href="{{ route('child.dashboard') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <a href="{{ route('child.universal.skills') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Universal Skills</a>
                                @endif
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Login/Register buttons -->
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Log in</a>
                        <a href="{{ route('register') }}"
                            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-md transition">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 gradient-bg rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">S</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">SkillDNA</span>
                    </div>
                    <p class="text-gray-600 text-sm mt-2">Where learning builds futures.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="{{ route('impact') }}" class="text-gray-600 hover:text-gray-900">Our Impact</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">Privacy</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">Terms</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">Contact</a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 text-center text-gray-500 text-sm">
                © {{ date('Y') }} SkillDNA. All rights reserved.
            </div>
        </div>
    </footer>

    @livewireScripts
    <!-- Scripts -->
    @stack('scripts')

    <!-- Alpine.js for interactivity -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any global JavaScript here
        });
    </script>
</body>

</html>
