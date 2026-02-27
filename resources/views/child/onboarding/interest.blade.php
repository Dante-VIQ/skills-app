@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                What do you want to learn? 🎯
            </h1>
            <p class="text-xl text-gray-600">
                Select at least 3 interests to personalize your learning experience
            </p>
        </div>

        <form method="POST" action="{{ route('onboarding.interests.save') }}" id="interestsForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @foreach($interestCategories as $key => $category)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <span class="text-3xl mr-3">{{ $category['icon'] }}</span>
                            <h3 class="text-xl font-bold">{{ $category['name'] }}</h3>
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($category['subcategories'] as $subcategory)
                            <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       name="interests[]" 
                                       value="{{ $key }}:{{ $subcategory }}"
                                       class="interest-checkbox w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-gray-700">{{ $subcategory }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center">
                <button type="submit" 
                        id="submitBtn"
                        disabled
                        class="px-8 py-4 bg-slate-400 text-white rounded-lg text-xl font-semibold cursor-not-allowed">
                    Select at least 3 interests to continue
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.interest-checkbox');
        const submitBtn = document.getElementById('submitBtn');
        
        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.interest-checkbox:checked').length;
            
            if (checkedCount >= 3) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-slate-400', 'cursor-not-allowed');
                submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                submitBtn.textContent = 'Continue →';
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('bg-slate-400', 'cursor-not-allowed');
                submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                submitBtn.textContent = 'Select at least 3 interests to continue';
            }
        }
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateButtonState);
        });

        // evaluate initial state in case some boxes are pre-checked
        updateButtonState();
    });
</script>
@endpush
@endsection