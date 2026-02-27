@extends('layouts.onboarding')

@section('title', 'Your Interests')

@section('step', '3')
@section('progress', '50%')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl overflow-hidden" 
     x-data="{
        selected: [],
        minSelected: 3,
        maxSelected: 6,
        
        toggleInterest(id) {
            const index = this.selected.indexOf(id);
            if (index === -1) {
                if (this.selected.length < this.maxSelected) {
                    this.selected.push(id);
                }
            } else {
                this.selected.splice(index, 1);
            }
        },
        
        save() {
            if (this.selected.length < this.minSelected) {
                alert('Please select at least ' + this.minSelected + ' interests!');
                return;
            }
            
            fetch('{{ route('child.onboarding.step2.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ interests: this.selected })
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
    <div class="bg-gradient-to-r from-green-500 to-teal-600 p-6">
        <h2 class="text-2xl font-bold text-white">What do you like?</h2>
        <p class="text-white/90">Choose <span x-text="minSelected"></span> to <span x-text="maxSelected"></span> things</p>
    </div>

    <!-- Content -->
    <div class="p-8">
        <!-- Counter -->
        <div class="mb-6 flex justify-between items-center">
            <span class="text-gray-600">Selected: <span x-text="selected.length"></span></span>
            <span class="text-sm text-gray-500">Pick up to 6</span>
        </div>
        
        <!-- Interests Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
            @foreach($interests as $interest)
            <button @click="toggleInterest('{{ $interest['id'] }}')"
                    :class="{
                        'selected border-{{ $interest['color'] }}-500 bg-{{ $interest['color'] }}-50': selected.includes('{{ $interest['id'] }}'),
                        'border-gray-200 hover:border-{{ $interest['color'] }}-300 hover:bg-{{ $interest['color'] }}-50': !selected.includes('{{ $interest['id'] }}')
                    }"
                    class="interest-card p-4 border-2 rounded-xl text-center transition">
                <div class="text-3xl mb-2">{{ $interest['icon'] }}</div>
                <h3 class="font-medium text-gray-900">{{ $interest['name'] }}</h3>
            </button>
            @endforeach
        </div>
        
        <!-- Navigation -->
        <div class="flex justify-between">
            <a href="{{ route('child.onboarding.step1') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                ← Back
            </a>
            <button @click="save" 
                    class="px-6 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:opacity-90"
                    :disabled="selected.length < minSelected">
                Next Step →
            </button>
        </div>
    </div>
</div>
@endsection