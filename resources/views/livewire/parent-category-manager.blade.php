{{-- resources/views/livewire/parent-category-manager.blade.php --}}
<div class="category-manager">
    <!-- Child Selector -->
    <div class="child-selector mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Child</label>
        <div class="flex space-x-2">
            @foreach($children as $child)
                <button wire:click="selectChild({{ $child->id }})" 
                        class="px-4 py-2 rounded-lg border {{ $selectedChild->id == $child->id ? 'bg-blue-100 border-blue-500' : 'bg-white border-gray-300' }}">
                    {{ $child->getDisplayName() }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Universal Skills Requirements -->
    <div class="universal-skills">
        <h3 class="text-lg font-semibold mb-4">Universal Skill Requirements</h3>
        <p class="text-sm text-gray-600 mb-4">Set minimum weekly watch time for essential skills</p>
        
        <div class="space-y-4">
            @foreach($categories as $category)
                @php
                    $watchedMinutes = $childWatchTime[$category->id] ?? 0;
                    $requiredMinutes = $universalRequirements[$category->id]->required_minutes_per_week ?? 0;
                    $progress = $requiredMinutes > 0 ? min(100, ($watchedMinutes / $requiredMinutes) * 100) : 0;
                @endphp
                
                <div class="category-card border rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-lg">{{ $category->name }}</h4>
                            <p class="text-sm text-gray-600">
                                Watched this week: <strong>{{ $watchedMinutes }}min</strong>
                                @if($requiredMinutes > 0)
                                    / {{ $requiredMinutes }}min required
                                @endif
                            </p>
                        </div>
                        
                        <div class="w-32">
                            <input type="number" 
                                   wire:change="updateRequirement({{ $category->id }}, $event.target.value)"
                                   value="{{ $requiredMinutes }}"
                                   min="0"
                                   max="120"
                                   class="w-full p-2 border rounded"
                                   placeholder="Min per week">
                            <p class="text-xs text-gray-500 text-center mt-1">minutes/week</p>
                        </div>
                    </div>
                    
                    @if($requiredMinutes > 0)
                        <div class="progress-bar bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" 
                                 style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="text-xs text-gray-600 mt-1">
                            @if($watchedMinutes >= $requiredMinutes)
                                <span class="text-green-600">✅ Requirement met this week</span>
                            @else
                                <span class="text-orange-600">⏳ Needs {{ $requiredMinutes - $watchedMinutes }} more minutes</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>