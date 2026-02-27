{{-- resources/views/livewire/admin/content-curator.blade.php --}}
<div>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">Add New Learning Resource</h2>

        <div class="grid grid-cols-2 gap-6">
            {{-- Left: Source Selection --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">1. Choose Source Type</h3>

                <div class="flex space-x-4">
                    <button wire:click="setSourceType('youtube')"
                            class="px-4 py-2 {{ $sourceType == 'youtube' ? 'bg-blue-600 text-white' : 'bg-gray-200' }} rounded">
                        🎥 YouTube
                    </button>
                    <button wire:click="setSourceType('khan')"
                            class="px-4 py-2 {{ $sourceType == 'khan' ? 'bg-blue-600 text-white' : 'bg-gray-200' }} rounded">
                        📚 Khan Academy
                    </button>
                    <button wire:click="setSourceType('w3schools')"
                            class="px-4 py-2 {{ $sourceType == 'w3schools' ? 'bg-blue-600 text-white' : 'bg-gray-200' }} rounded">
                        💻 W3Schools
                    </button>
                    <button wire:click="setSourceType('robotics')"
                            class="px-4 py-2 {{ $sourceType == 'robotics' ? 'bg-blue-600 text-white' : 'bg-gray-200' }} rounded">
                        🤖 Robotics
                    </button>
                </div>

                @if($sourceType == 'youtube')
                    <div>
                        <label class="block text-sm font-medium mb-2">YouTube URL or Search</label>
                        <input type="text" wire:model="youtubeInput"
                               class="w-full border rounded p-2"
                               placeholder="Paste URL or search term...">
                        <button wire:click="searchOrImport"
                                class="mt-2 px-4 py-2 bg-green-600 text-white rounded">
                            {{ str_contains($youtubeInput, 'youtube.com') ? 'Import Video' : 'Search' }}
                        </button>

                        @if($searchResults)
                            <div class="mt-4 space-y-2 max-h-96 overflow-y-auto">
                                @foreach($searchResults as $result)
                                    <div class="border p-3 rounded hover:bg-gray-50 cursor-pointer"
                                         wire:click="selectVideo('{{ $result['id']['videoId'] }}')">
                                        <img src="{{ $result['snippet']['thumbnails']['high']['url'] }}"
                                             class="w-32 h-24 object-cover float-left mr-3">
                                        <h4 class="font-bold">{{ $result['snippet']['title'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $result['snippet']['channelTitle'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                @if($sourceType == 'robotics')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Project Title</label>
                            <input type="text" wire:model="roboticsTitle" class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Difficulty</label>
                            <select wire:model="difficulty" class="w-full border rounded p-2">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Materials Needed</label>
                            <textarea wire:model="materials" class="w-full border rounded p-2"
                                      placeholder="List required items..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Code Samples</label>
                            <textarea wire:model="codeSamples" class="w-full border rounded p-2 font-mono"
                                      placeholder="Paste code here..."></textarea>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: Metadata & Attribution --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">2. Add Educational Context</h3>

                <div>
                    <label class="block text-sm font-medium">Your Summary (required)</label>
                    <textarea wire:model="summary" class="w-full border rounded p-2"
                              placeholder="Explain this in your own words..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium">Learning Objectives</label>
                    <div class="space-y-2">
                        @foreach($objectives as $index => $objective)
                            <div class="flex">
                                <input type="text" wire:model="objectives.{{ $index }}"
                                       class="flex-1 border rounded p-2 mr-2">
                                <button wire:click="removeObjective({{ $index }})" class="text-red-600">×</button>
                            </div>
                        @endforeach
                        <button wire:click="addObjective" class="text-blue-600 text-sm">+ Add Objective</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Discussion Questions</label>
                    <textarea wire:model="questions" class="w-full border rounded p-2"
                              placeholder="Questions for kids to think about..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium">Attribution</label>
                    <input type="text" wire:model="attribution" class="w-full border rounded p-2 bg-gray-100"
                           value="{{ $attribution ?? '' }}" readonly>
                    <p class="text-xs text-gray-500 mt-1">We always credit the original source</p>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button wire:click="saveDraft" class="px-6 py-2 bg-gray-600 text-white rounded">
                        Save Draft
                    </button>
                    <button wire:click="publish" class="px-6 py-2 bg-blue-600 text-white rounded">
                        Publish Resource
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
