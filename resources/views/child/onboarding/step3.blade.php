@extends('layouts.onboarding')

@section('title', 'Learning Goals')

@section('step', '4')
@section('progress', '75%')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl overflow-hidden" 
     x-data="{
        selected: [],
        
        toggleGoal(id) {
            const index = this.selected.indexOf(id);
            if (index === -1) {
                this.selected.push(id);
            } else {
                this.selected.splice(index, 1);
            }
        },
        
        save() {
            if (this.selected.length === 0) {
                alert('Please select at least one goal!');
                return;
            }
            
            fetch('{{ route('child.onboarding.step3.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ goals: this.selected })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.next_url;
                }
            });
        }
     }">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
        <h2 class="text-2xl font-bold text-white">What do you want to achieve?</h2>
        <p class="text-white/90">Select your learning goals</p>
    </div>

    <!-- Content -->
    <div class="p-8">
        <div class="space-y-4 mb-8">
            @foreach($goals as $goal)
            <button @click="toggleGoal('{{ $goal['id'] }}')"
                    :class="{ 'bg-purple-50 border-purple-500': selected.includes('{{ $goal['id'] }}') }"
                    class="w-full p-4 border-2 border-gray-200 rounded-xl text-left hover:border-purple-300 transition flex items-center">
                <div class="text-3xl mr-4">{{ $goal['icon'] }}</div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900">{{ $goal['name'] }}</h3>
                </div>
                <div x-show="selected.includes('{{ $goal['id'] }}')">
                    <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                </div>
            </button>
            @endforeach
        </div>
        
        <!-- Navigation -->
        <div class="flex justify-between">
            <a href="{{ route('child.onboarding.step2') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                ← Back
            </a>
            <button @click="save" 
                    class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:opacity-90">
                Next Step →
            </button>
        </div>
    </div>
</div>
@endsection