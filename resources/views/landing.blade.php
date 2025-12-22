<x-app-layout>
{{-- @extends('layouts.app') --}}

@section('title', 'Home')

@section('content')
<div class="relative overflow-hidden">
    <!-- Hero Section -->
    <div class="relative pt-12 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8">
        <!-- Background elements -->
        <div class="absolute inset-0">
            <div class="absolute inset-y-0 left-0 w-1/2 bg-gradient-to-r from-blue-50 to-purple-50"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto">
            <div class="text-center">
                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Where Your Child's</span>
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                        Curiosity Builds Schools
                    </span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Every video watched, every skill learned, every creative project — generates resources to build schools, labs, and libraries for children in need.
                </p>
                <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                        <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 md:py-4 md:text-lg md:px-10">
                            Start Free Trial
                        </a>
                    </div>
                    <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                        <a href="#how-it-works" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                            See How It Works
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact Stats -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">{{ $impactStats['children_learning'] ?? '0' }}</div>
                    <div class="text-blue-100">Children Learning</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">{{ $impactStats['projects_funded'] ?? '0' }}</div>
                    <div class="text-blue-100">Projects Funded</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">{{ $impactStats['total_impact'] ?? '0' }}</div>
                    <div class="text-blue-100">Children Impacted</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">{{ $impactStats['active_projects'] ?? '0' }}</div>
                    <div class="text-blue-100">Active Projects</div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div id="how-it-works" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">How Learning Builds Futures</h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                    A beautiful cycle of learning and giving
                </p>
            </div>

            <div class="mt-12">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div class="absolute left-1/2 transform -translate-x-1/2 md:left-0 md:transform-none md:relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold text-lg">
                                1
                            </div>
                        </div>
                        <div class="mt-8 md:mt-0 md:ml-16">
                            <h3 class="text-lg font-medium text-gray-900">Child Learns</h3>
                            <p class="mt-2 text-base text-gray-500">
                                AI discovers your child's interests and creates a personalized learning journey
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <div class="absolute left-1/2 transform -translate-x-1/2 md:left-0 md:transform-none md:relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-purple-500 to-purple-600 text-white font-bold text-lg">
                                2
                            </div>
                        </div>
                        <div class="mt-8 md:mt-0 md:ml-16">
                            <h3 class="text-lg font-medium text-gray-900">We Generate</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Each activity generates passive income through ethical partnerships
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative">
                        <div class="absolute left-1/2 transform -translate-x-1/2 md:left-0 md:transform-none md:relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold text-lg">
                                3
                            </div>
                        </div>
                        <div class="mt-8 md:mt-0 md:ml-16">
                            <h3 class="text-lg font-medium text-gray-900">We Build</h3>
                            <p class="mt-2 text-base text-gray-500">
                                100% of generated funds go to building educational infrastructure
                            </p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative">
                        <div class="absolute left-1/2 transform -translate-x-1/2 md:left-0 md:transform-none md:relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-green-500 to-green-600 text-white font-bold text-lg">
                                4
                            </div>
                        </div>
                        <div class="mt-8 md:mt-0 md:ml-16">
                            <h3 class="text-lg font-medium text-gray-900">You See Impact</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Watch real-time updates as your child's learning transforms communities
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 py-16">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                <span class="block">Ready to make a difference?</span>
                <span class="block text-blue-600">Start your child's learning journey today.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">
                        Get started for free
                    </a>
                </div>
                <div class="ml-3 inline-flex rounded-md shadow">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50">
                        Log in
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</x-app-layout>