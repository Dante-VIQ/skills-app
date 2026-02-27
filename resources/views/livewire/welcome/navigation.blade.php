<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-xl">S</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">SkillDNA</span>
                    </div>
                </div>

            </div>
            @guest
                <!-- Settings Dropdown -->
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Log in</a>
                <a href="{{ route('register') }}"
                    class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-md transition">
                    Get Started
                </a>

            @endguest
        </div>
    </div>

</nav>
