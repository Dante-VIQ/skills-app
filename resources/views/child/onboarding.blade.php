<x-app-layout>

    @section('title', 'Welcome to SkillDNA!')


    <style>
        .onboarding-step {
            opacity: 0.5;
            transform: scale(0.95);
            transition: all 0.3s ease;
        }

        .onboarding-step.active {
            opacity: 1;
            transform: scale(1);
        }

        .interest-option {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .interest-option:hover {
            transform: translateY(-2px);
        }

        .interest-option.selected {
            border-color: #6366f1;
            background-color: #eef2ff;
            transform: scale(1.05);
        }
    </style>



    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="onboardingWizard()">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-2xl font-bold text-gray-900">Welcome to SkillDNA! 🎉</h2>
                <span class="text-sm font-semibold text-blue-600">Step <span x-text="currentStep"></span> of 3</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                    :style="`width: ${(currentStep / 3) * 100}%`"></div>
            </div>
        </div>

        <!-- Step 1: Choose Interests -->
        <div x-show="currentStep === 1" class="onboarding-step active" x-transition>
            <div class="text-center mb-8">
                <div
                    class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-4xl">🎯</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">What are you interested in?</h3>
                <p class="text-gray-600">Choose at least 3 things you love! This helps us find the perfect learning
                    activities for you.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                @foreach ([['icon' => '🤖', 'name' => 'Robots', 'color' => 'blue'], ['icon' => '🎨', 'name' => 'Art', 'color' => 'pink'], ['icon' => '🌿', 'name' => 'Nature', 'color' => 'green'], ['icon' => '🎵', 'name' => 'Music', 'color' => 'purple'], ['icon' => '🏗️', 'name' => 'Building', 'color' => 'orange'], ['icon' => '📚', 'name' => 'Stories', 'color' => 'indigo'], ['icon' => '🔬', 'name' => 'Science', 'color' => 'teal'], ['icon' => '⚽', 'name' => 'Sports', 'color' => 'red'], ['icon' => '🐾', 'name' => 'Animals', 'color' => 'yellow'], ['icon' => '🚀', 'name' => 'Space', 'color' => 'gray']] as $interest)
                    <div class="interest-option border-2 border-gray-200 rounded-xl p-4 text-center"
                        :class="{ 'selected': selectedInterests.includes('{{ $interest['name'] }}') }"
                        @click="toggleInterest('{{ $interest['name'] }}')">
                        <div class="text-3xl mb-2">{{ $interest['icon'] }}</div>
                        <div class="font-semibold text-gray-900">{{ $interest['name'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <div class="text-sm text-gray-500 mb-4">
                    Selected: <span x-text="selectedInterests.length"></span> interests
                    <span x-show="selectedInterests.length < 3" class="text-red-500 ml-2">(Choose at least 3)</span>
                </div>
                <button @click="nextStep()" :disabled="selectedInterests.length < 3"
                    :class="selectedInterests.length >= 3 ? 'bg-gradient-to-r from-blue-500 to-purple-600 hover:shadow-md' :
                        'bg-gray-300 cursor-not-allowed'"
                    class="px-8 py-3 text-white rounded-lg font-semibold transition">
                    Continue to Next Step →
                </button>
            </div>
        </div>

        <!-- Step 2: Scenario Questions -->
        <div x-show="currentStep === 2" class="onboarding-step" x-transition>
            <div class="text-center mb-8">
                <div
                    class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-green-100 to-teal-100 rounded-full flex items-center justify-center">
                    <span class="text-4xl">🧩</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Let's Play a Game!</h3>
                <p class="text-gray-600">Answer these fun questions to help us understand how you like to learn.</p>
            </div>

            <div class="max-w-2xl mx-auto">
                <template x-for="(question, index) in scenarioQuestions" :key="index">
                    <div x-show="currentQuestion === index" class="mb-8" x-transition>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4" x-text="question.question"></h4>
                        <div class="space-y-3">
                            <template x-for="(option, optionIndex) in question.options" :key="optionIndex">
                                <button @click="selectAnswer(index, optionIndex)"
                                    :class="question.selected === optionIndex ? 'border-blue-500 bg-blue-50' :
                                        'border-gray-200 hover:border-blue-300'"
                                    class="w-full text-left p-4 border-2 rounded-lg transition">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 flex items-center justify-center mr-3"
                                            :class="question.selected === optionIndex ?
                                                'border-blue-500 bg-blue-100 text-blue-600' :
                                                'border-gray-300 text-gray-400'">
                                            <span x-text="String.fromCharCode(65 + optionIndex)"></span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900" x-text="option.text"></div>
                                            <div class="text-sm text-gray-500 mt-1" x-text="option.skills.join(', ')">
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="flex justify-between mt-8">
                    <button @click="prevQuestion()" x-show="currentQuestion > 0"
                        class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        ← Previous
                    </button>
                    <button @click="nextQuestion()"
                        :class="scenarioQuestions[currentQuestion].selected !== null ?
                            'bg-gradient-to-r from-blue-500 to-purple-600 hover:shadow-md' :
                            'bg-gray-300 cursor-not-allowed'"
                        class="px-6 py-2 text-white rounded-lg font-semibold transition ml-auto">
                        <span
                            x-text="currentQuestion < scenarioQuestions.length - 1 ? 'Next Question →' : 'Continue to Final Step →'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Quick Challenge -->
        <div x-show="currentStep === 3" class="onboarding-step" x-transition>
            <div class="text-center mb-8">
                <div
                    class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full flex items-center justify-center">
                    <span class="text-4xl">🚀</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Quick Brain Teaser!</h3>
                <p class="text-gray-600">Just 3 fun questions to power up your profile.</p>
            </div>

            <div class="max-w-2xl mx-auto">
                <template x-for="(challenge, index) in challenges" :key="index">
                    <div x-show="currentChallenge === index" class="mb-8" x-transition>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4" x-text="challenge.question"></h4>

                        <template x-if="challenge.type === 'multiple_choice'">
                            <div class="space-y-3">
                                <template x-for="(option, optionIndex) in challenge.options" :key="optionIndex">
                                    <button @click="selectChallengeAnswer(index, optionIndex)"
                                        :class="challenge.selected === optionIndex ? 'border-blue-500 bg-blue-50' :
                                            'border-gray-200 hover:border-blue-300'"
                                        class="w-full text-left p-4 border-2 rounded-lg transition">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 flex items-center justify-center mr-3"
                                                :class="challenge.selected === optionIndex ?
                                                    'border-blue-500 bg-blue-100 text-blue-600' :
                                                    'border-gray-300 text-gray-400'">
                                                <span x-text="String.fromCharCode(65 + optionIndex)"></span>
                                            </div>
                                            <div class="font-medium text-gray-900" x-text="option"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-if="challenge.type === 'open_ended'">
                            <div>
                                <textarea x-model="challenge.answer" placeholder="Type your answer here..."
                                    class="w-full p-4 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200"
                                    rows="4"></textarea>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex justify-between mt-8">
                    <button @click="prevChallenge()" x-show="currentChallenge > 0"
                        class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        ← Previous
                    </button>
                    <button @click="nextChallenge()" :disabled="!challenges[currentChallenge].answered"
                        :class="challenges[currentChallenge].answered ?
                            'bg-gradient-to-r from-blue-500 to-purple-600 hover:shadow-md' :
                            'bg-gray-300 cursor-not-allowed'"
                        class="px-6 py-2 text-white rounded-lg font-semibold transition ml-auto">
                        <span
                            x-text="currentChallenge < challenges.length - 1 ? 'Next Challenge →' : 'Complete Onboarding!'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div x-show="showSuccess"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50" x-transition>
            <div class="bg-white rounded-2xl p-8 max-w-md w-full text-center">
                <div
                    class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-green-400 to-teal-500 rounded-full flex items-center justify-center text-white text-3xl">
                    🎉
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Welcome to SkillDNA!</h3>
                <p class="text-gray-600 mb-6">Your personalized learning journey is ready. Let's start building futures
                    together!</p>
                <button @click="completeOnboardingAndRedirect()"
                    class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-lg font-semibold hover:shadow-md transition">
                    Start Learning →
                </button>
            </div>
        </div>
    </div>

    <script>
        function onboardingWizard() {
            return {
                currentStep: 1,
                selectedInterests: [],
                scenarioQuestions: [{
                        question: "Your team is building the coolest treehouse ever! What's your main job?",
                        selected: null,
                        options: [{
                                text: "Draw the awesome design so everyone knows what to build",
                                skills: ["Creativity", "Visualization"]
                            },
                            {
                                text: "Figure out how much wood and nails you'll need",
                                skills: ["Logical Thinking", "Planning"]
                            },
                            {
                                text: "Make sure everyone is happy and has a job they like",
                                skills: ["Empathy", "Collaboration"]
                            }
                        ]
                    },
                    {
                        question: "You find a mysterious, old map. What do you do first?",
                        selected: null,
                        options: [{
                                text: "Research online to see if it's a known treasure",
                                skills: ["Research", "Analysis"]
                            },
                            {
                                text: "Gather your friends and start the adventure right away!",
                                skills: ["Initiative", "Risk-taking"]
                            },
                            {
                                text: "Study the map's symbols to decode its secret meaning",
                                skills: ["Pattern Recognition", "Critical Thinking"]
                            }
                        ]
                    }
                ],
                currentQuestion: 0,
                challenges: [{
                        question: "If you have two coins that add up to 15 cents, and one of them is not a nickel, what are the coins?",
                        type: "multiple_choice",
                        options: ["Two nickels", "A dime and a nickel", "A dime and a penny", "Two dimes"],
                        selected: null,
                        get answered() {
                            return this.selected !== null;
                        }
                    },
                    {
                        question: "How many uses can you think of for a paperclip?",
                        type: "open_ended",
                        answer: "",
                        get answered() {
                            return this.answer.trim().length > 0;
                        }
                    },
                    {
                        question: "Your friend looks sad after losing a game. What do you say?",
                        type: "multiple_choice",
                        options: [
                            "That's okay, I lose all the time too",
                            "Want to play again? I'll let you win this time",
                            "You played really well! Want to talk about it?",
                            "Don't worry, it's just a game"
                        ],
                        selected: null,
                        get answered() {
                            return this.selected !== null;
                        }
                    }
                ],
                currentChallenge: 0,
                showSuccess: false,

                toggleInterest(interest) {
                    const index = this.selectedInterests.indexOf(interest);
                    if (index > -1) {
                        this.selectedInterests.splice(index, 1);
                    } else {
                        this.selectedInterests.push(interest);
                    }
                },

                nextStep() {
                    if (this.currentStep === 1 && this.selectedInterests.length >= 3) {
                        this.currentStep++;
                    } else if (this.currentStep === 2 && this.scenarioQuestions.every(q => q.selected !== null)) {
                        this.currentStep++;
                    }
                },

                selectAnswer(questionIndex, optionIndex) {
                    this.scenarioQuestions[questionIndex].selected = optionIndex;
                },

                nextQuestion() {
                    if (this.scenarioQuestions[this.currentQuestion].selected !== null) {
                        if (this.currentQuestion < this.scenarioQuestions.length - 1) {
                            this.currentQuestion++;
                        } else {
                            this.nextStep();
                        }
                    }
                },

                prevQuestion() {
                    if (this.currentQuestion > 0) {
                        this.currentQuestion--;
                    }
                },

                selectChallengeAnswer(challengeIndex, optionIndex) {
                    this.challenges[challengeIndex].selected = optionIndex;
                },

                nextChallenge() {
                    if (this.challenges[this.currentChallenge].answered) {
                        if (this.currentChallenge < this.challenges.length - 1) {
                            this.currentChallenge++;
                        } else {
                            this.completeOnboarding();
                        }
                    }
                },

                prevChallenge() {
                    if (this.currentChallenge > 0) {
                        this.currentChallenge--;
                    }
                },

                completeOnboarding() {
                    this.showSuccess = true;
                },

                getOnboardingData() {
                    return {
                        interests: this.selectedInterests,
                        scenarioAnswers: this.scenarioQuestions.map(q => q.selected),
                        challengeAnswers: this.challenges.map(c => c.type === 'multiple_choice' ? c.selected : c.answer)
                    };
                }

                completeOnboardingAndRedirect() {
                    // Save data via AJAX
                    axios.post('{{ route('child.onboarding') }}', {
                        onboarding_data: this.getOnboardingData(),
                        _token: '{{ csrf_token() }}'
                    }).then(response => {
                        // Redirect to dashboard
                        window.location.href = "{{ route('child.home') }}";
                    }).catch(error => {
                        console.error('Error saving onboarding:', error);
                        // Still redirect even if save fails
                        window.location.href = "{{ route('child.home') }}";
                    });
                }
            }
        }
    </script>
</x-app-layout>
