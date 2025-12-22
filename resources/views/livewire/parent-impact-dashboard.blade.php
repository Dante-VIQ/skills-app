{{-- resources/views/livewire/parent-impact-dashboard.blade.php --}}
<div class="impact-dashboard">
    <!-- Child Selector -->
    <div class="child-selector mb-6">
        <h3 class="text-lg font-semibold mb-3">Select Child to View Impact</h3>
        <div class="flex space-x-2">
            @foreach($children as $child)
                <button wire:click="selectChild({{ $child->id }})" 
                        class="px-4 py-2 rounded-lg border {{ $selectedChild->id == $child->id ? 'bg-green-100 border-green-500' : 'bg-white border-gray-300' }}">
                    {{ $child->getDisplayName() }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Child's Personal Impact -->
    <div class="personal-impact mb-8">
        <h3 class="text-xl font-bold mb-4">{{ $selectedChild->getDisplayName() }}'s Impact</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="stat-card text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $childContributions['videos_watched'] }}</div>
                <div class="text-sm text-gray-600">Videos Watched</div>
            </div>
            <div class="stat-card text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $childContributions['creative_projects'] }}</div>
                <div class="text-sm text-gray-600">Projects Created</div>
            </div>
            <div class="stat-card text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $childContributions['estimated_impact']['books_provided'] }}</div>
                <div class="text-sm text-gray-600">Books Provided</div>
            </div>
            <div class="stat-card text-center p-4 bg-orange-50 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">₦{{ number_format($childContributions['estimated_impact']['contribution_amount'] * 500, 0) }}</div>
                <div class="text-sm text-gray-600">Value Generated</div>
            </div>
        </div>
    </div>

    <!-- Global Impact -->
    <div class="global-impact mb-8">
        <h3 class="text-xl font-bold mb-4">Our Collective Impact</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="stat-card text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ number_format($globalImpact['total_children_helped']) }}</div>
                <div class="text-sm text-gray-600">Children Impacted</div>
            </div>
            <div class="stat-card text-center p-4 bg-indigo-50 rounded-lg">
                <div class="text-2xl font-bold text-indigo-600">{{ $globalImpact['schools_built'] }}</div>
                <div class="text-sm text-gray-600">Schools Built</div>
            </div>
            <div class="stat-card text-center p-4 bg-teal-50 rounded-lg">
                <div class="text-2xl font-bold text-teal-600">{{ $globalImpact['labs_created'] }}</div>
                <div class="text-sm text-gray-600">Labs Created</div>
            </div>
            <div class="stat-card text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">₦{{ number_format($globalImpact['current_funding'], 0) }}</div>
                <div class="text-sm text-gray-600">Funds Deployed</div>
            </div>
        </div>
    </div>

    <!-- Active Projects -->
    <div class="active-projects">
        <h3 class="text-xl font-bold mb-4">Projects You're Helping Build</h3>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($recentProjects as $project)
                <div class="project-card border rounded-lg overflow-hidden">
                    <img src="{{ $project->progress_photos[0] ?? '/images/default-project.jpg' }}" 
                         alt="{{ $project->name }}" class="w-full h-32 object-cover">
                    <div class="p-4">
                        <h4 class="font-semibold mb-2">{{ $project->name }}</h4>
                        <p class="text-sm text-gray-600 mb-3">{{ $project->location }}</p>
                        
                        <div class="progress-bar bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-green-500 h-2 rounded-full" 
                                 style="width: {{ ($project->amount_raised / $project->target_amount) * 100 }}%"></div>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>₦{{ number_format($project->amount_raised, 0) }} raised</span>
                            <span>{{ number_format(($project->amount_raised / $project->target_amount) * 100, 0) }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>