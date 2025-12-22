<x-app-layout>

@section('title', 'Our Impact')


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl p-8 mb-8 text-white">
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Building Futures Through Learning</h1>
            <p class="text-green-100 text-lg mb-6">
                Every child's learning activity on SkillDNA generates resources to build schools, 
                labs, and libraries for children in underserved communities.
            </p>
            <div class="flex items-center space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold">₦{{ number_format($totalImpact['total_raised'] ?? 0, 0) }}</div>
                    <div class="text-green-100 text-sm">Total Generated</div>
                </div>
                <div class="h-8 w-px bg-green-400"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ number_format($totalImpact['total_impacted'] ?? 0) }}</div>
                    <div class="text-green-100 text-sm">Children Impacted</div>
                </div>
                <div class="h-8 w-px bg-green-400"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $totalImpact['completed_projects'] ?? 0 }}</div>
                    <div class="text-green-100 text-sm">Projects Completed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">How Your Learning Makes a Difference</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="skilldna-card bg-white p-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <span class="text-blue-600 text-xl">📚</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">You Learn</h3>
                <p class="text-gray-600">Children engage with personalized, interest-based learning content</p>
            </div>
            <div class="skilldna-card bg-white p-6">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <span class="text-purple-600 text-xl">💰</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">We Generate</h3>
                <p class="text-gray-600">Each activity generates revenue through ethical partnerships and sponsorships</p>
            </div>
            <div class="skilldna-card bg-white p-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <span class="text-green-600 text-xl">🏫</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">We Build</h3>
                <p class="text-gray-600">100% of generated funds go to building educational infrastructure</p>
            </div>
        </div>
    </div>

    <!-- Featured Projects -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Featured Projects</h2>
            <a href="{{ route('impact.projects') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                View all projects →
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(isset($projects) && $projects->count() > 0)
                @foreach($projects->take(3) as $project)
                    <div class="skilldna-card bg-white overflow-hidden">
                        <div class="h-48 bg-gradient-to-r from-{{ $project->type === 'school' ? 'blue' : ($project->type === 'lab' ? 'purple' : 'green') }}-400 to-{{ $project->type === 'school' ? 'blue' : ($project->type === 'lab' ? 'purple' : 'green') }}-600"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="px-2 py-1 bg-{{ $project->type === 'school' ? 'blue' : ($project->type === 'lab' ? 'purple' : 'green') }}-100 text-{{ $project->type === 'school' ? 'blue' : ($project->type === 'lab' ? 'purple' : 'green') }}-600 rounded text-xs font-semibold uppercase">
                                    {{ $project->type }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">{{ $project->location }}</span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $project->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($project->description, 100) }}</p>
                            
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">₦{{ number_format($project->amount_raised, 0) }} raised</span>
                                    <span class="font-semibold">{{ number_format($project->getProgressPercentage(), 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" 
                                         style="width: {{ $project->getProgressPercentage() }}%"></div>
                                </div>
                            </div>
                            
                            <a href="{{ route('project.updates', $project) }}" 
                               class="block text-center px-4 py-2 border-2 border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition">
                                View Updates
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-3 text-center py-12 bg-gray-50 rounded-2xl">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No projects yet</h3>
                    <p class="text-gray-500">Check back soon for our first impact projects!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Join the Movement?</h2>
        <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
            Sign up today and start your child's learning journey while helping build schools for children in need.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ auth()->user()->isParent() ? route('parent.add-child') : route('child.dashboard') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg font-semibold hover:shadow-md transition">
                    Continue Your Journey
                </a>
            @else
                <a href="{{ route('register') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg font-semibold hover:shadow-md transition">
                    Get Started Free
                </a>
                <a href="{{ route('login') }}" 
                   class="px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition">
                    Log In
                </a>
            @endauth
        </div>
    </div>
</div>
</x-app-layout>