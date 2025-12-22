<div class="assignment-blocker">
    @if($isCompleted)
        <div class="completion-message text-center py-8">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-2xl font-bold text-green-600 mb-2">Assignment Complete!</h2>
            <p class="text-gray-600 mb-4">You can now continue exploring your learning feed.</p>
            <a href="{{ route('child.dashboard') }}" class="btn btn-primary">
                Back to Learning
            </a>
        </div>
    @else
        <div class="assignment-header bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex items-center">
                <div class="text-2xl mr-3">📝</div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Assignment from Parent</h1>
                    <p class="text-gray-600">Complete this to continue learning</p>
                </div>
            </div>
        </div>

        <div class="assignment-content">
            <h2 class="text-2xl font-bold mb-4">{{ $assignment->title }}</h2>
            <p class="text-gray-700 mb-6">{{ $assignment->description }}</p>

            <form wire:submit="submitAssignment">
                @foreach($assignment->content as $index => $question)
                    <div class="question-card mb-6 p-4 border rounded-lg">
                        <h3 class="font-semibold mb-3">Question {{ $index + 1 }}</h3>
                        <p class="mb-3">{{ $question['question'] }}</p>
                        
                        @if($question['type'] === 'multiple_choice')
                            <div class="space-y-2">
                                @foreach($question['options'] as $optionIndex => $option)
                                    <label class="flex items-center space-x-3">
                                        <input type="radio" 
                                               wire:model="answers.{{ $index }}" 
                                               value="{{ $optionIndex }}"
                                               class="text-blue-600">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <textarea wire:model="answers.{{ $index }}" 
                                      class="w-full p-2 border rounded"
                                      rows="3"
                                      placeholder="Your answer..."></textarea>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary w-full py-3">
                    Submit Assignment
                </button>
            </form>
        </div>
    @endif
</div>