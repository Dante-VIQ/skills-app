@extends('layouts.onboarding')

@section('title', 'Quick Tour')

@section('step', '5')
@section('progress', '100%')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl overflow-hidden" 
     x-data="{
        step: 1,
        next() {
            if (this.step < 3) {
                this.step++;
            } else {
                this.complete();
            }
        },
        prev() {
            if (this.step > 1) {
                this.step--;
            }
        },
        complete() {
            document.getElementById('complete-form').submit();
        }
     }">
    
    <!-- Progress dots -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
        <div class="flex justify-center space-x-2">
            <template x-for="i in 3" :key="i">
                <div :class="step >= i ? 'bg-white' : 'bg-white/40'" 
                     class="w-3 h-3 rounded-full transition"></div>
            </template>
        </div>
    </div>

    <!-- Tour steps -->
    <div class="p-8">
        <!-- Step 1 -->
        <div x-show="step === 1" class="text-center">
            <div class="text-6xl mb-6 floating">📚</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Your Learning Feed</h3>
            <p class="text-gray-600 mb-8">
                You'll get personalized videos and activities based on your interests!
            </p>
            <img src="https://placehold.co/600x300/667eea/white?text=Learning+Feed+Preview" 
                 class="rounded-lg shadow-lg mb-8">
        </div>
        
        <!-- Step 2 -->
        <div x-show="step === 2" class="text-center">
            <div class="text-6xl mb-6 floating">🏆</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Earn Rewards</h3>
            <p class="text-gray-600 mb-8">
                Complete activities to earn points and help real-world projects!
            </p>
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-yellow-50 p-4 rounded-xl">
                    <div class="text-2xl mb-2">⭐</div>
                    <div class="font-bold">Points</div>
                </div>
                <div class="bg-green-50 p-4 rounded-xl">
                    <div class="text-2xl mb-2">🏅</div>
                    <div class="font-bold">Badges</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-xl">
                    <div class="text-2xl mb-2">🌍</div>
                    <div class="font-bold">Impact</div>
                </div>
            </div>
        </div>
        
        <!-- Step 3 -->
        <div x-show="step === 3" class="text-center">
            <div class="text-6xl mb-6 floating">👨‍👩‍👧</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Parents Can Help</h3>
            <p class="text-gray-600 mb-8">
                Your parents can see your progress and create fun assignments for you!
            </p>
            <div class="bg-purple-50 p-6 rounded-xl mb-8">
                <p class="text-purple-800">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Don't worry - everything is safe and approved by parents!
                </p>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="flex justify-between items-center">
            <button x-show="step > 1" 
                    @click="prev" 
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                ← Back
            </button>
            <div x-show="step === 1" class="flex-1"></div>
            
            <button @click="next" 
                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold hover:opacity-90">
                <span x-text="step === 3 ? 'Let\'s Start!' : 'Next'"></span>
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>
    
    <!-- Complete Form -->
    <form id="complete-form" method="POST" action="{{ route('child.onboarding.complete') }}" class="hidden">
        @csrf
    </form>
</div>
@endsection