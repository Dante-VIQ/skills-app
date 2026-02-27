<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SkillDNA') - SkillDNA</title>

    <!-- Fonts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        @auth
        <nav class="bg-white/80 backdrop-blur-lg fixed w-full z-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-dna text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-700 bg-clip-text text-transparent">
                        SkillDNA
                    </span>
                </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            {{-- Role-based navigation --}}
                            @role('parent')
                                <a href="{{ route('dashboard') }}"
                                   class="{{ request()->routeIs('parent.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('parent.children') }}"
                                   class="{{ request()->routeIs('parent.children*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    My Children
                                </a>
                                <a href="{{ route('parent.assignments') }}"
                                   class="{{ request()->routeIs('parent.assignments*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Assignments
                                </a>
                            @endrole

                            @role('child')
                                <a href="{{ route('child.dashboard') }}"
                                   class="{{ request()->routeIs('child.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Home
                                </a>
                                <a href="{{ route('child.learn') }}"
                                   class="{{ request()->routeIs('child.learn*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Learn
                                </a>
                                <a href="{{ route('child.assignments') }}"
                                   class="{{ request()->routeIs('child.assignments*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    My Tasks
                                </a>
                            @endrole

                            @role('tutor')
                                <a href="{{ route('tutor.dashboard') }}"
                                   class="{{ request()->routeIs('tutor.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('tutor.students') }}"
                                   class="{{ request()->routeIs('tutor.students*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Students
                                </a>
                            @endrole

                            @role('master')
                                <a href="{{ route('master.dashboard') }}"
                                   class="{{ request()->routeIs('master.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('master.users') }}"
                                   class="{{ request()->routeIs('master.users*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Users
                                </a>
                                <a href="{{ route('master.content') }}"
                                   class="{{ request()->routeIs('master.content*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Content
                                </a>
                            @endrole
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                    <div>
                                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                        <div class="text-xs text-gray-400">
                                            {{ Auth::user()->role_name }}
                                        </div>
                                    </div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <!-- Account Management -->
                                <x-dropdown-link href="{{ route('profile.edit') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Role-specific links -->
                                @role('parent')
                                    <x-dropdown-link href="{{ route('parent.add-child') }}">
                                        Add Child
                                    </x-dropdown-link>
                                    {{-- <x-dropdown-link href="{{ route('parent.impact') }}">
                                        Family Impact
                                    </x-dropdown-link> --}}
                                @endrole

                                @role('child')
                                    <x-dropdown-link href="{{ route('child.progress') }}">
                                        My Progress
                                    </x-dropdown-link>
                                @endrole

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </nav>
        @endauth

        <!-- Page Content -->
        <main>
            {{-- {{ $slot }} --}}
            @yield('content')
        </main>
    </div>

    @livewireScripts
    <!-- page-specific scripts -->
    @stack('scripts')
</body>
</html>
