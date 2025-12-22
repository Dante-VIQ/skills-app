@section('title', 'My Learning Dashboard')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Hello, {{ auth()->user()->getDisplayName() }}! 🎉</h1>
                <p class="text-green-100">Ready for today's learning adventure?</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="flex items-center space-x-2">
                    <div class="px-3 py-1 bg-white/20 rounded-full text-sm">
                        <span class="font-semibold">Level 1</span> Explorer
                    </div>
                    <div class="w-24 bg-white/30 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full" style="width: 25%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Assignments Alert -->
    @if(auth()->user()->hasPendingAssignments())
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        You have a pending assignment from your parent
                    </h3>
                    <div class="mt-2">
                        <a href="{{ route('child.assignment.show', auth()->user()->getNextPendingAssignment()) }}" 
                           class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200">
                            Complete Assignment to Continue Learning
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Learning Feed -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Today's Learning Feed</h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Based on your interests:</span>
                <div class="flex space-x-1">
                    @foreach(auth()->user()->interests->take(3) as $interest)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">
                            {{ $interest->icon }} {{ $interest->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Example Learning Card 1 -->
            <div class="skilldna-card bg-white overflow-hidden">
                <div class="h-40 bg-gradient-to-r from-blue-400 to-blue-600"></div>
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded text-xs font-semibold">VIDEO</span>
                        <span class="ml-2 text-sm text-gray-500">8 min</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">How to Think Like a Scientist</h3>
                    <p class="text-gray-600 text-sm mb-4">Learn the scientific method through fun experiments</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                🔬
                            </div>
                            <span class="text-sm text-gray-700">Science Skills</span>
                        </div>
                        <button class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg text-sm font-semibold hover:shadow-md transition">
                            Watch
                        </button>
                    </div>
                </div>
            </div>

            <!-- Example Learning Card 2 -->
            <div class="skilldna-card bg-white overflow-hidden">
                <div class="h-40 bg-gradient-to-r from-purple-400 to-purple-600"></div>
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="px-2 py-1 bg-purple-100 text-purple-600 rounded text-xs font-semibold">INTERACTIVE</span>
                        <span class="ml-2 text-sm text-gray-500">15 min</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Create Your First Story</h3>
                    <p class="text-gray-600 text-sm mb-4">Interactive storytelling with character creation</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                📚
                            </div>
                            <span class="text-sm text-gray-700">Creativity</span>
                        </div>
                        <button class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg text-sm font-semibold hover:shadow-md transition">
                            Start
                        </button>
                    </div>
                </div>
            </div>

            <!-- Example Learning Card 3 -->
            <div class="skilldna-card bg-white overflow-hidden">
                <div class="h-40 bg-gradient-to-r from-green-400 to-green-600"></div>
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-semibold">CHALLENGE</span>
                        <span class="ml-2 text-sm text-gray-500">10 min</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Budget Your Lemonade Stand</h3>
                    <p class="text-gray-600 text-sm mb-4">Learn about money and basic business skills</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-teal-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                💰
                            </div>
                            <span class="text-sm text-gray-700">Financial Literacy</span>
                        </div>
                        <button class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-sm font-semibold hover:shadow-md transition">
                            Try Challenge
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-6">
            <button class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                Load More Activities
            </button>
        </div>
    </div>

    <!-- My Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Skills Progress -->
        <div class="lg:col-span-2 skilldna-card bg-white p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">My Skills Progress</h3>
            <div class="space-y-4">
                @foreach(auth()->user()->skills->take(4) as $skill)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $skill->name }}</span>
                            <span class="text-sm font-semibold text-blue-600">Level {{ $skill->pivot->proficiency_level ?? 1 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" 
                                 style="width: {{ min(100, ($skill->pivot->proficiency_level ?? 1) * 20) }}%"></div>
                        </div>
                    </div>
                @endforeach
                
                @if(auth()->user()->skills->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500">Complete activities to unlock skills!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- My Impact -->
        <div class="skilldna-card bg-white p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">My Impact</h3>
            <div class="space-y-4">
                <div class="text-center p-4 bg-gradient-to-br from-green-50 to-teal-50 rounded-xl">
                    <div class="text-3xl font-bold text-green-600 mb-2">₦0</div>
                    <div class="text-sm text-gray-600">Generated for charity</div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Videos Watched</span>
                        <span class="font-semibold">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Activities Completed</span>
                        <span class="font-semibold">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Skills Unlocked</span>
                        <span class="font-semibold">{{ auth()->user()->skills->count() }}</span>
                    </div>
                </div>
                
                <a href="{{ route('impact.portal') }}" class="block text-center mt-4 px-4 py-2 border-2 border-green-600 text-green-600 rounded-lg font-semibold hover:bg-green-50 transition">
                    See Projects You're Helping
                </a>
            </div>
        </div>
    </div>
</div>