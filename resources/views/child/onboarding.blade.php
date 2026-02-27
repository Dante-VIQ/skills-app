@extends('onboarding.layout')

@section('title', 'Welcome!')

@section('step', '1')
@section('progress', '0%')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl overflow-hidden" 
     x-data="{
        showConfetti: false,
        init() {
            setTimeout(() => this.showConfetti = true, 500);
        }
     }">
    
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-yellow-400 to-orange-400 p-8 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
                <pattern id="stars" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1" fill="white"/>
                </pattern>
                <rect width="100%" height="100%" fill="url(#stars)"/>
            </svg>
        </div>
        
        <div class="relative">
            <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center text-5xl mb-4 floating">
                🎉
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">
                Welcome, {{ auth()->user()->first_name }}!
            </h1>
            <p class="text-white/90 text-lg">
                Let's set up your learning adventure!
            </p>
        </div>
    </div>
    
    <!-- Content -->
    <div class="p-8">
        <div class="text-center mb-8">
            <p class="text-gray-600 text-lg">
                We'll help you create your profile and find the best content just for you.
                It'll only take a few minutes!
            </p>
        </div>
        
        <!-- Features preview -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-blue-50 p-4 rounded-xl text-center">
                <div class="text-3xl mb-2">🎨</div>
                <h3 class="font-bold text-blue-800">Pick Avatar</h3>
                <p class="text-xs text-blue-600">Choose how you look</p>
            </div>
            <div class="bg-green-50 p-4 rounded-xl text-center">
                <div class="text-3xl mb-2">⭐</div>
                <h3 class="font-bold text-green-800">Your Interests</h3>
                <p class="text-xs text-green-600">Tell us what you like</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-xl text-center">
                <div class="text-3xl mb-2">🎯</div>
                <h3 class="font-bold text-purple-800">Learning Goals</h3>
                <p class="text-xs text-purple-600">Set your targets</p>
            </div>
            <div class="bg-pink-50 p-4 rounded-xl text-center">
                <div class="text-3xl mb-2">🎮</div>
                <h3 class="font-bold text-pink-800">Quick Tour</h3>
                <p class="text-xs text-pink-600">See how it works</p>
            </div>
        </div>
        
        <!-- Start button -->
        <div class="text-center">
            <a href="{{ route('child.onboarding.step1') }}" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xl font-bold rounded-full hover:opacity-90 transform hover:scale-105 transition shadow-lg">
                <span>Let's Go!</span>
                <i class="fas fa-arrow-right ml-3"></i>
            </a>
        </div>
    </div>
</div>
@endsection