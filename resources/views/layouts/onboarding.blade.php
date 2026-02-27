<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome to SkillDNA!')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .onboarding-progress-bar {
            transition: width 0.5s ease;
        }
        .interest-card {
            transition: all 0.2s ease;
        }
        .interest-card.selected {
            transform: scale(1.05);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-400 via-purple-400 to-pink-400 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="py-4 px-6">
            <div class="max-w-4xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-brain text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-white font-bold text-xl">SkillDNA Kids</span>
                </div>
                
                <!-- Skip button -->
                <form method="POST" action="{{ route('child.onboarding.skip') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:text-white/80 text-sm font-medium">
                        Skip for now <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center p-6">
            <div class="w-full max-w-3xl">
                @yield('content')
            </div>
        </main>

        <!-- Footer with progress -->
        <footer class="py-4 px-6">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center text-white text-sm mb-2">
                    <span>Getting started</span>
                    <span>Step @yield('step', 1) of 4</span>
                </div>
                <div class="h-2 bg-white/30 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full onboarding-progress-bar" 
                         style="width: @yield('progress', '25%')"></div>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>