<x-app-layout>

@section('title', 'Add Child')


<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center">
            <span class="text-3xl">👶</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Add Your Child</h1>
        <p class="text-gray-600">Set up your child's learning profile to start their SkillDNA journey</p>
    </div>

    <!-- Form -->
    <div class="skilldna-card bg-white p-6 md:p-8">
        <form action="{{ route('parent.children.store') }}" method="POST">
            @csrf

            <!-- Child's Name -->
            <div class="mb-6">
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Child's First Name *
                </label>
                <input type="text" 
                       id="first_name" 
                       name="first_name" 
                       required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
                       placeholder="e.g., Sarah">
                @error('first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Child's Last Name *
                </label>
                <input type="text" 
                       id="last_name" 
                       name="last_name" 
                       required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
                       placeholder="e.g., Johnson">
                @error('last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Birth Date -->
            <div class="mb-6">
                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Date of Birth *
                </label>
                <input type="date" 
                       id="birth_date" 
                       name="birth_date" 
                       required
                       max="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                @error('birth_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Initial Interests (Optional) -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    What does your child enjoy? (Optional)
                </label>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                    @foreach([
                        ['icon' => '🤖', 'name' => 'robots', 'label' => 'Robots'],
                        ['icon' => '🎨', 'name' => 'art', 'label' => 'Art'],
                        ['icon' => '🌿', 'name' => 'nature', 'label' => 'Nature'],
                        ['icon' => '🎵', 'name' => 'music', 'label' => 'Music'],
                        ['icon' => '🏗️', 'name' => 'building', 'label' => 'Building'],
                        ['icon' => '📚', 'name' => 'stories', 'label' => 'Stories'],
                        ['icon' => '🔬', 'name' => 'science', 'label' => 'Science'],
                        ['icon' => '⚽', 'name' => 'sports', 'label' => 'Sports'],
                        ['icon' => '🐾', 'name' => 'animals', 'label' => 'Animals'],
                        ['icon' => '🚀', 'name' => 'space', 'label' => 'Space'],
                    ] as $interest)
                        <div class="relative">
                            <input type="checkbox" 
                                   id="interest_{{ $interest['name'] }}" 
                                   name="interests[]" 
                                   value="{{ $interest['name'] }}"
                                   class="hidden peer">
                            <label for="interest_{{ $interest['name'] }}" 
                                   class="block p-3 border-2 border-gray-200 rounded-lg text-center cursor-pointer transition peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                <div class="text-2xl mb-1">{{ $interest['icon'] }}</div>
                                <div class="text-xs font-medium text-gray-700">{{ $interest['label'] }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Universal Skills Setup -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Essential Learning Goals</h3>
                <p class="text-gray-600 text-sm mb-4">Set weekly learning goals for essential skills</p>
                
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-blue-600">💰</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Financial Literacy</h4>
                                    <p class="text-sm text-gray-600">Understanding money and basic finance</p>
                                </div>
                            </div>
                            <div class="w-32">
                                <input type="number" 
                                       name="financial_literacy_minutes" 
                                       min="0" 
                                       max="120" 
                                       value="30"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center">
                                <p class="text-xs text-gray-500 text-center mt-1">min/week</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-green-600">💬</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Communication Skills</h4>
                                    <p class="text-sm text-gray-600">Effective speaking and listening</p>
                                </div>
                            </div>
                            <div class="w-32">
                                <input type="number" 
                                       name="communication_minutes" 
                                       min="0" 
                                       max="120" 
                                       value="20"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center">
                                <p class="text-xs text-gray-500 text-center mt-1">min/week</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" 
                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold text-center hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg font-semibold hover:shadow-md transition">
                    Add Child & Start Learning Journey
                </button>
            </div>
        </form>
    </div>

    <!-- Help Text -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500">
            After adding your child, they'll need to complete a quick, fun onboarding process
            to personalize their learning experience.
        </p>
    </div>
</div>


</x-app-layout>