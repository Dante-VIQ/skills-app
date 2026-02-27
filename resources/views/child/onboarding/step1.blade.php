@extends('layouts.onboarding')

@section('title', 'Choose Avatar')

@section('step', '2')
@section('progress', '25%')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl overflow-hidden" 
     x-data="{
        step: 'choose',
        avatarType: 'default',
        avatarEmoji: '😊',
        previewUrl: null,
        selectedFile: null,
        
        emojis: ['😊', '😎', '🥳', '🦁', '🐼', '🐨', '🦊', '🐸', '🐧', '🦄', '🐱', '🐶'],
        
        selectEmoji(emoji) {
            this.avatarEmoji = emoji;
            this.avatarType = 'emoji';
            this.step = 'preview';
        },
        
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
                this.previewUrl = URL.createObjectURL(file);
                this.avatarType = 'upload';
                this.step = 'preview';
            }
        },
        
        useDefault() {
            this.avatarType = 'default';
            this.step = 'preview';
        },
        
        reset() {
            this.step = 'choose';
            this.avatarType = 'default';
            if (this.previewUrl) {
                URL.revokeObjectURL(this.previewUrl);
            }
        },
        
        save() {
            const formData = new FormData();
            formData.append('avatar_type', this.avatarType);
            
            if (this.avatarType === 'emoji') {
                formData.append('avatar_emoji', this.avatarEmoji);
            } else if (this.avatarType === 'upload' && this.selectedFile) {
                formData.append('avatar', this.selectedFile);
            }
            
            fetch('{{ route('child.onboarding.step1.save') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.next_url;
                }
            })
            .catch(error => console.error('Error:', error));
        }
     }">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-center">
        <h2 class="text-2xl font-bold text-white">Create Your Avatar</h2>
        <p class="text-white/90">How would you like to look?</p>
    </div>

    <!-- Content -->
    <div class="p-8">
        <template x-if="step === 'choose'">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Upload Photo -->
                <button @click="$refs.fileInput.click()" 
                        class="group p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition">
                    <div class="text-4xl mb-3">📸</div>
                    <h3 class="font-bold text-gray-900">Upload Photo</h3>
                    <p class="text-sm text-gray-500">Use your own picture</p>
                </button>
                <input type="file" x-ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden">
                
                <!-- Choose Emoji -->
                <button @click="step = 'emoji'" 
                        class="p-6 border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition">
                    <div class="text-4xl mb-3">😊</div>
                    <h3 class="font-bold text-gray-900">Fun Emoji</h3>
                    <p class="text-sm text-gray-500">Pick a cool emoji</p>
                </button>
                
                <!-- Default Avatar -->
                <button @click="useDefault" 
                        class="p-6 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition">
                    <div class="text-4xl mb-3">🎨</div>
                    <h3 class="font-bold text-gray-900">Default Avatar</h3>
                    <p class="text-sm text-gray-500">Use colorful initials</p>
                </button>
            </div>
        </template>

        <template x-if="step === 'emoji'">
            <div>
                <div class="grid grid-cols-6 gap-3 mb-6">
                    <template x-for="emoji in emojis" :key="emoji">
                        <button @click="selectEmoji(emoji)" 
                                class="text-3xl p-3 hover:bg-purple-100 rounded-lg transition">
                            <span x-text="emoji"></span>
                        </button>
                    </template>
                </div>
                <button @click="step = 'choose'" 
                        class="text-gray-500 hover:text-gray-700">
                    ← Back
                </button>
            </div>
        </template>

        <template x-if="step === 'preview'">
            <div class="text-center">
                <div class="mb-6">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-purple-500 shadow-xl">
                        <template x-if="avatarType === 'upload' && previewUrl">
                            <img :src="previewUrl" class="w-full h-full object-cover">
                        </template>
                        <template x-if="avatarType === 'emoji'">
                            <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-5xl">
                                <span x-text="avatarEmoji"></span>
                            </div>
                        </template>
                        <template x-if="avatarType === 'default'">
                            <div class="w-full h-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-3xl font-bold">
                                {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                            </div>
                        </template>
                    </div>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <button @click="reset" 
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Change
                    </button>
                    <button @click="save" 
                            class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:opacity-90">
                        Looks Good! →
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection